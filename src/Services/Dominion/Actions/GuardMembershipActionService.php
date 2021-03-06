<?php

namespace OpenDominion\Services\Dominion\Actions;

use OpenDominion\Exceptions\GameException;
use OpenDominion\Models\Dominion;
use OpenDominion\Services\Dominion\GuardMembershipService;
use OpenDominion\Traits\DominionGuardsTrait;

class GuardMembershipActionService
{
    use DominionGuardsTrait;

    /** @var GuardMembershipService */
    protected $guardMembershipService;

    /**
     * GuardMembershipActionService constructor.
     *
     * @param GuardMembershipService $guardMembershipService
     */
    public function __construct(GuardMembershipService $guardMembershipService)
    {
        $this->guardMembershipService = $guardMembershipService;
    }

    /**
     * Starts royal guard application for a Dominion.
     *
     * @param Dominion $dominion
     * @return array
     * @throws GameException
     */
    public function joinRoyalGuard(Dominion $dominion): array
    {
        $this->guardLockedDominion($dominion);

        if (!$this->guardMembershipService->canJoinGuards($dominion)) {
            throw new GameException('You cannot join the Royal Guard for the first five days of the round.');
        }

        if ($this->guardMembershipService->isRoyalGuardMember($dominion)) {
            throw new GameException('You are already a member of the Royal Guard.');
        }

        if ($this->guardMembershipService->isRoyalGuardApplicant($dominion)) {
            throw new GameException('You have already applied to join the Royal Guard.');
        }

        if($dominion->race->getPerkValue('cannot_join_guards'))
        {
            throw new GameException($dominion->race->name . ' is not able to join the guards.');
        }

        $this->guardMembershipService->joinRoyalGuard($dominion);

        return [
            'message' => sprintf(
                'You have applied to join the Royal Guard.'
            ),
            'data' => []
        ];
    }

    /**
     * Starts elite guard application for a Dominion.
     *
     * @param Dominion $dominion
     * @return array
     * @throws GameException
     */
    public function joinEliteGuard(Dominion $dominion): array
    {
        $this->guardLockedDominion($dominion);

        if (!$this->guardMembershipService->isRoyalGuardMember($dominion)) {
            throw new GameException('You must already be a member of the Royal Guard.');
        }

        if ($this->guardMembershipService->isEliteGuardMember($dominion)) {
            throw new GameException('You are already a member of the Elite Guard.');
        }

        if ($this->guardMembershipService->isEliteGuardApplicant($dominion)) {
            throw new GameException('You have already applied to join the Elite Guard.');
        }

        if($dominion->race->getPerkValue('cannot_join_guards'))
        {
            throw new GameException($dominion->race->name . ' is not able to join the guards.');
        }

        $this->guardMembershipService->joinEliteGuard($dominion);

        return [
            'message' => sprintf(
                'You have applied to join the Elite Guard.'
            ),
            'data' => []
        ];
    }

    /**
     * Leaves the royal guard or cancels an application for a Dominion.
     *
     * @param Dominion $dominion
     * @return array
     * @throws GameException
     */
    public function leaveRoyalGuard(Dominion $dominion): array
    {
        $this->guardLockedDominion($dominion);

        if ($this->guardMembershipService->getHoursBeforeLeaveRoyalGuard($dominion)) {
            throw new GameException('You cannot leave the Emperor\'s Royal Guard for 48 hours after joining.');
        }

        if ($this->guardMembershipService->isEliteGuardApplicant($dominion)) {
            throw new GameException('You must first cancel your Elite Guard application.');
        }

        if ($this->guardMembershipService->isEliteGuardMember($dominion)) {
            throw new GameException('You must first leave the Elite Guard.');
        }

        if (!$this->guardMembershipService->isRoyalGuardApplicant($dominion) && !$this->guardMembershipService->isRoyalGuardMember($dominion)) {
            throw new GameException('You are not a member of the Royal Guard.');
        }

        if ($this->guardMembershipService->isRoyalGuardApplicant($dominion)) {
            $message = 'You have canceled your Royal Guard application.';
        } else {
            $message = 'You have left the Royal Guard.';
        }

        $this->guardMembershipService->leaveRoyalGuard($dominion);

        return [
            'message' => $message,
            'data' => []
        ];
    }

    /**
     * Leaves the elite guard or cancels an application for a Dominion.
     *
     * @param Dominion $dominion
     * @return array
     * @throws GameException
     */
    public function leaveEliteGuard(Dominion $dominion): array
    {
        $this->guardLockedDominion($dominion);

        if ($this->guardMembershipService->getHoursBeforeLeaveEliteGuard($dominion)) {
            throw new GameException('You cannot leave the Emperor\'s Elite Guard for 48 hours after joining.');
        }

        if (!$this->guardMembershipService->isEliteGuardApplicant($dominion) && !$this->guardMembershipService->isEliteGuardMember($dominion)) {
            throw new GameException('You are not a member of the Elite Guard.');
        }

        if ($this->guardMembershipService->isEliteGuardApplicant($dominion)) {
            $message = 'You have canceled your Elite Guard application.';
        } else {
            $message = 'You have left the Elite Guard.';
        }

        $this->guardMembershipService->leaveEliteGuard($dominion);

        return [
            'message' => $message,
            'data' => []
        ];
    }
}
