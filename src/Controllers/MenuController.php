<?php

namespace Harimayco\Menu\Controllers;

use Harimayco\Menu\Facades\Menu;
use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;  // Use your controller from the Base module
use Harimayco\Menu\Models\Menus;
use Harimayco\Menu\Models\MenuItems;

class MenuController extends Controller
{
    public function createnewmenu()
    {
        $menu = new Menus();
        $menu->name = request()->input("menuname");
        $menu->locale = session('locale');
        $menu->save();

        return response()->json(["resp" => $menu->id]);
    }

    public function deleteitemmenu()
    {
        $menuitem = MenuItems::find(request()->input("id"));
        if ($menuitem) {
            $menuitem->delete();
        }
    }

    public function deletemenug()
    {
        $getall = MenuItems::where('menu', request()->input("id"))->get();
        if ($getall->isEmpty()) {
            $menudelete = Menus::find(request()->input("id"));
            if ($menudelete) {
                $menudelete->delete();
            }
            return response()->json(["resp" => "you delete this item"]);
        } else {
            return response()->json([
                "resp" => "You have to delete all items first",
                "error" => 1
            ]);
        }
    }

    public function updateitem()
    {
        $arraydata = request()->input("arraydata");

        if (is_array($arraydata)) {
            foreach ($arraydata as $value) {
                $menuitem = MenuItems::find($value['id'] ?? null);
                if (!$menuitem) continue;

                $menuitem->label = $value['label'] ?? '';
                $menuitem->link = $value['link'] ?? '';
                $menuitem->class = $value['class'] ?? '';
                if (config('menu.use_roles')) {
                    $menuitem->role_id = $value['role_id'] ?? 0;
                }
                $menuitem->save();
            }
        } else {
            $menuitem = MenuItems::find(request()->input("id"));
            if (!$menuitem) return;

            $menuitem->label = request()->input("label", '');
            $menuitem->link = request()->input("url", '');
            $menuitem->class = request()->input("clases", '');
            if (config('menu.use_roles')) {
                $menuitem->role_id = request()->input("role_id", 0);
            }
            $menuitem->save();
        }
    }

    public function addcustommenu()
    {
        $menuitem = new MenuItems();
        $menuitem->label = request()->input("labelmenu");
        $menuitem->link = request()->input("linkmenu");
        $menuitem->menu = request()->input("idmenu");
        $menuitem->sort = MenuItems::getNextSortRoot(request()->input("idmenu"));

        if (config('menu.use_roles')) {
            $menuitem->role_id = request()->input("rolemenu", 0);
        }

        $menuitem->save();
    }

    public function generatemenucontrol()
    {
        $menu = Menus::find(request()->input("idmenu"));
        if ($menu) {
            $menu->name = request()->input("menuname");
            $menu->locale = session('locale');
            $menu->save();
        }

        $arraydata = request()->input("arraydata");

        if (is_array($arraydata)) {
            foreach ($arraydata as $value) {
                $menuitem = MenuItems::find($value["id"] ?? null);
                if (!$menuitem) continue;

                $menuitem->parent = $value["parent"] ?? 0;
                $menuitem->sort = $value["sort"] ?? 0;
                $menuitem->depth = $value["depth"] ?? 0;
                if (config('menu.use_roles')) {
                    $menuitem->role_id = $value["role_id"] ?? 0;
                }
                $menuitem->save();
            }
        }

        return response()->json(["resp" => 1]);
    }
}
