<?php

namespace OpenDominion\Models;

use Carbon\Carbon;
use OpenDominion\Events\InfoOpCreatingEvent;

/**
 * OpenDominion\Models\InfoOp
 *
 * @property int $id
 * @property int $source_realm_id
 * @property int $source_dominion_id
 * @property int $target_dominion_id
 * @property string $type
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \OpenDominion\Models\Dominion $sourceDominion
 * @method static \Illuminate\Database\Eloquent\Builder|\OpenDominion\Models\InfoOp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\OpenDominion\Models\InfoOp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\OpenDominion\Models\InfoOp query()
 * @mixin \Eloquent
 */
class BlackOp extends AbstractModel
{
    protected $casts = [
        'source_realm_id' => 'int',
        'source_dominion_id' => 'int',
        'target_dominion_id' => 'int',
        'data' => 'array',
    ];

    protected $dispatchesEvents = [
        'creating' => InfoOpCreatingEvent::class,
    ];

    public function sourceRealm()
    {
//        return $this->belongsTo(Realm::class);
    }

    public function sourceDominion()
    {
        return $this->belongsTo(Dominion::class, 'source_dominion_id');
    }

    public function targetDominion()
    {
        return $this->belongsTo(Dominion::class, 'target_dominion_id');
    }

    public function targetRealm()
    {
        return $this->belongsTo(Realm::class, 'target_realm_id');
    }

}
