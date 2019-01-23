<?php
namespace App\Providers;
use App\Repositories\Bookmark\BookmarkInterface;
use App\Repositories\Bookmark\Bookmarkaccess;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Flyer\FlyerInterface;
use App\Repositories\Flyer\Flyeraccess;
use App\Repositories\Offers\OffersInterface;
use App\Repositories\Offers\Offersaccess;
use App\Repositories\User\UserInterface;
use App\Repositories\User\Useraccess;
use App\Repositories\Store\StoreInterface;
use App\Repositories\Store\Storeaccess;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bind the interface to an implementation repository/access class
     */
    public function register()
    {
        // DI for bookmark
        $this->app->bind(
            BookmarkInterface::class,
            Bookmarkaccess::class
        );

        $this->app->bind(
            FlyerInterface::class,
            Flyeraccess::class
        );

        $this->app->bind(
            OffersInterface::class,
            Offersaccess::class
        );

        $this->app->bind(
            UserInterface::class,
            Useraccess::class
        );

        $this->app->bind(
            StoreInterface::class,
            Storeaccess::class
        );
    }
}