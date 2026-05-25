<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Routing\Route;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  public function boot(): void
  {
    $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
    $horizontalMenuData = json_decode($horizontalMenuJson);

    // Share all menuData to all the views dynamically via View Composer
    View::composer('*', function ($view) use ($horizontalMenuData) {
      $verticalMenu = null;

      if (auth()->check()) {
        $role = auth()->user()->roles->first()?->name;

        $menuFile = match(true) {
          in_array($role, ['Super Admin', 'Dinas', 'Operator']) => 'verticalMenu-admin.json',
          in_array($role, ['Kepala Sekolah', 'Guru']) => 'verticalMenu-sekolah.json',
          in_array($role, ['Siswa', 'Orang Tua']) => 'verticalMenu-siswa.json',
          default => 'verticalMenu-admin.json'
        };

        $verticalMenuJson = file_get_contents(base_path("resources/menu/{$menuFile}"));
        $verticalMenu = json_decode($verticalMenuJson);
      } else {
        $verticalMenuJson = file_get_contents(base_path("resources/menu/verticalMenu-admin.json"));
        $verticalMenu = json_decode($verticalMenuJson);
      }

      $view->with('menuData', [$verticalMenu, $horizontalMenuData]);
    });
  }
}
