<?php

namespace OpenDominion\Providers;

use Cache;
use Illuminate\Contracts\View\View;
use OpenDominion\Calculators\NetworthCalculator;
use OpenDominion\Helpers\NotificationHelper;
use OpenDominion\Models\Council\Post;
use OpenDominion\Models\Council\Thread;
use OpenDominion\Models\Dominion;
use OpenDominion\Services\Dominion\SelectorService;

#ODA
#use OpenDominion\Calculators\Dominion\LandCalculator;
use OpenDominion\Calculators\Dominion\TechCalculator;

class ComposerServiceProvider extends AbstractServiceProvider
{

    /** @var TechCalculator */
    protected $techCalculator;

    public function __construct(TechCalculator $techCalculator)
    {
          $this->techCalculator = $techCalculator;
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.topnav', function (View $view) {
            $view->with('selectorService', app(SelectorService::class));
        });

        view()->composer('partials.main-sidebar', function (View $view) {
            $selectorService = app(SelectorService::class);

            if (!$selectorService->hasUserSelectedDominion()) {
                return;
            }

            /** @var Dominion $dominion */
            $dominion = $selectorService->getUserSelectedDominion();

            $lastRead = $dominion->council_last_read;

            $councilUnreadCount = $dominion->realm
                ->councilThreads()
                ->with('posts')
                ->get()
                ->map(static function (Thread $thread) use ($lastRead) {
                    $unreadCount = $thread->posts->filter(static function (Post $post) use ($lastRead) {
                        return $post->created_at > $lastRead;
                    })->count();

                    if ($thread->created_at > $lastRead) {
                        $unreadCount++;
                    }

                    return $unreadCount;
                })
                ->sum();


#            $xp = $dominion->resource_tech;
#            $level1TechCost = $this->techCalculator->getTechCost($selectedDominion, Null);

            $view->with('councilUnreadCount', $councilUnreadCount);
#            $view->with('highestLevelTechAfforded', $highestLevelTechAfforded);
        });

        view()->composer('partials.main-footer', function (View $view) {
            $version = (Cache::has('version-html') ? Cache::get('version-html') : 'unknown');
            $view->with('version', $version);
        });

        view()->composer('partials.notification-nav', function (View $view) {
            $view->with('notificationHelper', app(NotificationHelper::class));
        });

        // todo: do we need this here in this class?
        view()->composer('partials.resources-overview', function (View $view) {
            $view->with('networthCalculator', app(NetworthCalculator::class));
        });
    }
}
