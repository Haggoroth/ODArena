@extends('layouts.master')

@section('page-header', 'Status')

@section('content')
    <div class="row">

        <div class="col-sm-12 col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bar-chart"></i> The Dominion of {{ $selectedDominion->name }}</h3>
                </div>
                <div class="box-body no-padding">
                    <div class="row">

                        <div class="col-xs-12 col-sm-4">
                            <table class="table">
                                <colgroup>
                                    <col width="50%">
                                    <col width="50%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th colspan="2">Overview</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ruler:</td>
                                        <td>{{ $selectedDominion->ruler_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Faction:</td>
                                        <td>{{ $selectedDominion->race->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Land:</td>
                                        <td>{{ number_format($landCalculator->getTotalLand($selectedDominion)) }}</td>
                                    </tr>
                                    <tr>
                                      @if ($selectedDominion->race->name == 'Growth')
                                        <td>Cells:</td>
                                      @elseif ($selectedDominion->race->name == 'Myconid')
                                        <td>Spores:</td>
                                      @elseif ($selectedDominion->race->name == 'Swarm')
                                        <td>Larvae:</td>
                                      @else
                                        <td>Peasants:</td>
                                      @endif
                                        <td>{{ number_format($selectedDominion->peasants) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Employment:</td>
                                        <td>{{ number_format($populationCalculator->getEmploymentPercentage($selectedDominion), 2) }}%</td>
                                    </tr>
                                    <tr>
                                        <td>Networth:</td>
                                        <td>{{ number_format($networthCalculator->getDominionNetworth($selectedDominion)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Prestige:</td>
                                        <td>{{ number_format($selectedDominion->prestige) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Victories:</td>
                                        <td>{{ number_format($selectedDominion->stat_attacking_success) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-xs-12 col-sm-4">
                            <table class="table">
                                <colgroup>
                                    <col width="50%">
                                    <col width="50%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th colspan="2">Resources</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Platinum:</td>
                                        <td>{{ number_format($selectedDominion->resource_platinum) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Food:</td>
                                        <td>{{ number_format($selectedDominion->resource_food) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Lumber:</td>
                                        <td>{{ number_format($selectedDominion->resource_lumber) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Mana:</td>
                                        <td>{{ number_format($selectedDominion->resource_mana) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Ore:</td>
                                        <td>{{ number_format($selectedDominion->resource_ore) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Gems:</td>
                                        <td>{{ number_format($selectedDominion->resource_gems) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Experience Points:</td>
                                        <td>{{ number_format($selectedDominion->resource_tech) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Boats:</td>
                                        <td>{{ number_format(floor($selectedDominion->resource_boats + $queueService->getInvasionQueueTotalByResource($selectedDominion, "resource_boats"))) }}</td>
                                    </tr>
                                    @if ($selectedDominion->race->name == 'Norse')
                                    <tr>
                                        <td>Champions:</td>
                                        <td>{{ number_format($selectedDominion->resource_champion) }}</td>
                                    </tr>
                                    @elseif ($selectedDominion->race->name == 'Demon')
                                    <tr>
                                        <td>Souls:</td>
                                        <td>{{ number_format($selectedDominion->resource_soul) }}</td>
                                    </tr>
                                    @elseif ($selectedDominion->race->name == 'Snow Elf')
                                    <tr>
                                        <td>Wild yetis:</td>
                                        <td>{{ number_format($selectedDominion->resource_wild_yeti) }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="col-xs-12 col-sm-4">
                            <table class="table">
                                <colgroup>
                                    <col width="50%">
                                    <col width="50%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th colspan="2">Military</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Morale:</td>
                                        <td>{{ number_format($selectedDominion->morale) }}%</td>
                                    </tr>
                                    <tr>
                                      @if ($selectedDominion->race->name == 'Growth')
                                        <td>Amoeba:</td>
                                      @elseif ($selectedDominion->race->name == 'Myconid')
                                        <td>Sporelings:</td>
                                      @elseif ($selectedDominion->race->name == 'Swarm')
                                        <td>Cocoons:</td>
                                      @else
                                        <td>Draftees:</td>
                                      @endif
                                        <td>{{ number_format($selectedDominion->military_draftees) }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                          <span data-toggle="tooltip" data-placement="top" title="{{ $unitHelper->getUnitHelpString('unit1', $selectedDominion->race) }}">
                                              {{ $selectedDominion->race->units->get(0)->name }}:
                                          </span>
                                        </td>
                                        <td>{{ number_format($militaryCalculator->getTotalUnitsForSlot($selectedDominion, 1)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                          <span data-toggle="tooltip" data-placement="top" title="{{ $unitHelper->getUnitHelpString('unit2', $selectedDominion->race) }}">
                                              {{ $selectedDominion->race->units->get(1)->name }}:
                                          </span>
                                        </td>
                                        <td>{{ number_format($militaryCalculator->getTotalUnitsForSlot($selectedDominion, 2)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                          <span data-toggle="tooltip" data-placement="top" title="{{ $unitHelper->getUnitHelpString('unit3', $selectedDominion->race) }}">
                                              {{ $selectedDominion->race->units->get(2)->name }}:
                                          </span>
                                        </td>
                                        <td>{{ number_format($militaryCalculator->getTotalUnitsForSlot($selectedDominion, 3)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                          <span data-toggle="tooltip" data-placement="top" title="{{ $unitHelper->getUnitHelpString('unit4', $selectedDominion->race) }}">
                                              {{ $selectedDominion->race->units->get(3)->name }}:
                                          </span>
                                        </td>
                                        <td>{{ number_format($militaryCalculator->getTotalUnitsForSlot($selectedDominion, 4)) }}</td>
                                    </tr>

                                    @if ((bool)$selectedDominion->race->getPerkValue('cannot_train_spies') == False)
                                    <tr>
                                        <td>Spies:</td>
                                        <td>{{ number_format($selectedDominion->military_spies) }}</td>
                                    </tr>
                                    @endif

                                    @if ((bool)$selectedDominion->race->getPerkValue('cannot_train_wizards') == False)
                                    <tr>
                                        <td>Wizards:</td>
                                        <td>{{ number_format($selectedDominion->military_wizards) }}</td>
                                    </tr>
                                    @endif

                                    @if ((bool)$selectedDominion->race->getPerkValue('cannot_train_archmages') == False)
                                    <tr>
                                        <td>ArchMages:</td>
                                        <td>{{ number_format($selectedDominion->military_archmages) }}</td>
                                    @endif

                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-3">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Information</h3>
                </div>
                <div class="box-body">
                    <p>This section gives you a quick overview of your dominion.</p>
                    <p>Your total land size is {{ number_format($landCalculator->getTotalLand($selectedDominion)) }} and networth is {{ number_format($networthCalculator->getDominionNetworth($selectedDominion)) }}.</p>
                    <p><a href="{{ route('dominion.rankings', 'land') }}">My Rankings</a></p>
                </div>
            </div>
        </div>

        @if ($selectedDominion->realm->motd && ($selectedDominion->realm->motd_updated_at > now()->subDays(3)))
            <div class="col-sm-12 col-md-9">
                <div class="panel panel-warning">
                    <div class="panel-body">
                        <b>Message of the Day:</b> {{ $selectedDominion->realm->motd }}
                        <br/><small class="text-muted">Posted {{ $selectedDominion->realm->motd_updated_at }}</small>
                    </div>
                </div>
            </div>
        @endif

        @if ($dominionProtectionService->isUnderProtection($selectedDominion))

        <div class="col-sm-12 col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="ra ra-shield text-aqua"></i> Protection</h3>
                </div>
                  <div class="box-body">
                      <p>You are under a magical state of protection. You have <b>{{ $selectedDominion->protection_ticks }}</b> protection {{ str_plural('tick', $selectedDominion->protection_ticks) }} left.</p>
                      <p>During protection you cannot be attacked or attack other dominions. You can neither cast any offensive spells or engage in espionage.</p>
                      <p>Regularly scheduled ticks do not count towards your dominion while you are in protection.</p>
                      <p>Click the button below to proceed to the next tick.</p>
                      <form action="{{ route('dominion.status') }}" method="post" role="form" id="tick_form">
                      @csrf
                      <button type="submit"
                              class="btn btn-info"
                              {{ $selectedDominion->isLocked() ? 'disabled' : null }}
                              id="tick-button">
                          <i class="ra ra-shield"></i>
                          Proceed to next tick
                      </button>
                    </form>
                  </div>
            </div>
        </div>

        @endif

        <div class="col-sm-12 col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-newspaper-o"></i> Recent News</h3>
                </div>

                @if ($notifications->isEmpty())
                    <div class="box-body">
                        <p>No recent news.</p>
                    </div>
                @else
                    <div class="box-body">
                        <table class="table table-condensed no-border">
                            @foreach ($notifications as $notification)
                                @php
                                    $route = array_get($notificationHelper->getNotificationCategories(), "{$notification->data['category']}.{$notification->data['type']}.route", '#');

                                    if (is_callable($route)) {
                                        if (isset($notification->data['data']['_routeParams'])) {
                                            $route = $route($notification->data['data']['_routeParams']);
                                        } else {
                                            // fallback
                                            $route = '#';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <span class="text-muted">{{ $notification->created_at }}</span>
                                    </td>
                                    <td>
                                        @if ($route !== '#')<a href="{{ $route }}">@endif
                                            <i class="{{ array_get($notificationHelper->getNotificationCategories(), "{$notification->data['category']}.{$notification->data['type']}.iconClass", 'fa fa-question') }}"></i>
                                            {{ $notification->data['message'] }}
                                        @if ($route !== '#')</a>@endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="box-footer">
                        <div class="pull-right">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($selectedDominion->pack !== null)
            <div class="col-sm-12 col-md-3">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pack</h3>
                    </div>
                    <div class="box-body">
                        <p>You are in pack <em>{{$selectedDominion->pack->name}}</em> with:</p>
                        <ul>
                            @foreach ($selectedDominion->pack->dominions as $dominion)
                                <li>
                                    @if ($dominion->ruler_name === $dominion->name)
                                        <strong>{{ $dominion->name }}</strong>
                                    @else
                                        {{ $dominion->ruler_name }} of <strong>{{ $dominion->name }}</strong>
                                    @endif

                                    @if($dominion->ruler_name !== $dominion->user->display_name)
                                        ({{ $dominion->user->display_name }})
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <p>
                            Slots used: {{ $selectedDominion->pack->dominions->count() }} / {{ $selectedDominion->pack->size }}.
                            @if ($selectedDominion->pack->isFull())
                                (full)
                            @elseif ($selectedDominion->pack->isClosed())
                                (closed)
                            @endif
                        </p>
                        @if (!$selectedDominion->pack->isFull() && !$selectedDominion->pack->isClosed())
                            <p>Your pack will automatically close on <strong>{{ $selectedDominion->pack->getClosingDate() }}</strong> to make space for random players in your realm.</p>
                            @if ($selectedDominion->pack->creator_dominion_id === $selectedDominion->id)
                                <p>
                                    <form action="{{ route('dominion.misc.close-pack') }}" method="post">
                                        @csrf
                                        <button type="submit" class="btn btn-link" style="padding: 0;">Close Pack Now</button>
                                    </form>
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
