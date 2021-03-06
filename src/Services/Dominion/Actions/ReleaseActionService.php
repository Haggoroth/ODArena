<?php

namespace OpenDominion\Services\Dominion\Actions;

use OpenDominion\Exceptions\GameException;
use OpenDominion\Helpers\UnitHelper;
use OpenDominion\Models\Dominion;
use OpenDominion\Services\Dominion\HistoryService;
use OpenDominion\Traits\DominionGuardsTrait;

# ODA
use OpenDominion\Calculators\Dominion\MilitaryCalculator;
use OpenDominion\Services\Dominion\QueueService;

class ReleaseActionService
{
    use DominionGuardsTrait;

    /** @var UnitHelper */
    protected $unitHelper;

    /** @var MilitaryCalculator */
    protected $militaryCalculator;

    /** @var QueueService */
    protected $queueService;

    /**
     * ReleaseActionService constructor.
     *
     * @param UnitHelper $unitHelper
     */
    public function __construct(
        UnitHelper $unitHelper,
        QueueService $queueService,
        MilitaryCalculator $militaryCalculator
      )
    {
        $this->unitHelper = $unitHelper;
        $this->queueService = $queueService;
        $this->militaryCalculator = $militaryCalculator;
    }

    /**
     * Does a release troops action for a Dominion.
     *
     * @param Dominion $dominion
     * @param array $data
     * @return array
     * @throws GameException
     */
    public function release(Dominion $dominion, array $data): array
    {
        $this->guardLockedDominion($dominion);

        $data = array_map('\intval', $data);

        /*

        array(8) { ["draftees"]=> int(1) ["unit1"]=> int(0) ["unit2"]=> int(0) ["unit3"]=> int(0) ["unit4"]=> int(0) ["spies"]=> int(0) ["wizards"]=> int(0) ["archmages"]=> int(0) }

        */

        $troopsReleased = [];

        $totalTroopsToRelease = array_sum($data);

        $totalDrafteesToRelease = $data['draftees'];
        $totalSpiesToRelease = $data['spies'];
        $totalWizardsToRelease = $data['wizards'];
        $totalArchmagesToRelease = $data['archmages'];
        $totalMilitaryUnitsToRelease = $data['unit1'] + $data['unit2'] + $data['unit3'] + $data['unit4'];

        # Must be releasing something.
        if ($totalTroopsToRelease <= 0)
        {
            throw new GameException('Military release aborted due to bad input.');
        }

        $units = [
          1 => $data['unit1'],
          2 => $data['unit2'],
          3 => $data['unit3'],
          4 => $data['unit4']
        ];

        $rawDpRelease = $this->militaryCalculator->getDefensivePowerRaw($dominion, null, null, $units, true);

        # Special considerations for releasing military units.
        if($rawDpRelease > 0)
        {
            # Must have at least 1% morale to release.
            if ($dominion->morale < 1)
            {
                throw new GameException('You must have at least 1% morale to release units with defensive power.');
            }

            # Cannot release if recently invaded.
            if ($this->militaryCalculator->getRecentlyInvadedCount($dominion))
            {
                throw new GameException('You cannot release military units with defensive power if you have been recently invaded.');
            }

            # Cannot release if units returning from invasion.
            $totalUnitsReturning = 0;
            for ($slot = 1; $slot <= 4; $slot++)
            {
              $totalUnitsReturning += $this->queueService->getInvasionQueueTotalByResource($dominion, "military_unit{$slot}");
            }
            if ($totalUnitsReturning !== 0)
            {
                throw new GameException('You cannot release military units with defensive power if you have units returning from battle.');
            }

        }
        foreach ($data as $unitType => $amount) {
            if ($amount === 0) { // todo: collect()->except(amount == 0)
                continue;
            }

            if ($amount < 0) {
                throw new GameException('Military release aborted due to bad input.');
            }

            if ($amount > $dominion->{'military_' . $unitType}) {
                throw new GameException('Military release was not completed due to bad input.');
            }
        }

        foreach ($data as $unitType => $amount) {
            if ($amount === 0) {
                continue;
            }

            $dominion->{'military_' . $unitType} -= $amount;

            if ($unitType === 'draftees')
            {
                $dominion->peasants += $amount;
            }
            # Only return draftees if unit is not exempt from population.
            elseif (!$dominion->race->getUnitPerkValueForUnitSlot(intval(str_replace('unit','',$unitType)), 'does_not_count_as_population'))
            {
                $dominion->military_draftees += $amount;
            }

            $troopsReleased[$unitType] = $amount;
        }

        $dominion->save(['event' => HistoryService::EVENT_ACTION_RELEASE]);

        return [
            'message' => $this->getReturnMessageString($dominion, $troopsReleased),
            'data' => [
                'totalTroopsReleased' => $totalTroopsToRelease,
            ],
        ];
    }

    /**
     * Returns the message for a release action.
     *
     * @param Dominion $dominion
     * @param array $troopsReleased
     * @return string
     */
    protected function getReturnMessageString(Dominion $dominion, array $troopsReleased): string
    {
        $stringParts = ['You successfully released'];

        // Draftees into peasants
        if (isset($troopsReleased['draftees'])) {
            $amount = $troopsReleased['draftees'];
            $stringParts[] = sprintf('%s %s into the peasantry', number_format($amount), str_plural('draftee', $amount));
        }

        // Troops into draftees
        $troopsParts = [];
        foreach ($troopsReleased as $unitType => $amount) {
            if ($unitType === 'draftees') {
                continue;
            }

            $unitName = str_singular(strtolower($this->unitHelper->getUnitName($unitType, $dominion->race)));
            $troopsParts[] = (number_format($amount) . ' ' . str_plural($unitName, $amount));
        }

        if (!empty($troopsParts)) {
            if (\count($stringParts) === 2) {
                $stringParts[] = 'and';
            }

            $stringParts[] = generate_sentence_from_array($troopsParts);
            $stringParts[] = 'into draftees';
        }

        return (implode(' ', $stringParts) . '.');
    }
}
