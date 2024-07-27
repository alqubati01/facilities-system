<?php

namespace App\Http\Controllers;

use Akaunting\Firewall\Models\Ip;
use Illuminate\Http\Request;

class FirewallController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $ips = Ip::all();
    return view('content.firewalls.index', ['ips' => $ips]);
  }

  public function blocked(string $id)
  {
    $ip = Ip::findOrFail($id);

    $ip->blocked = 1;
    $ip->update();

    return redirect()->route('firewalls.index')
      ->with('success', 'تم حظر عنوان هذا IP:'. $ip->ip . ' بنجاح');
  }

  public function unblocked(string $id)
  {
    $ip = Ip::findOrFail($id);

    $ip->blocked = 0;
    $ip->update();

    return redirect()->route('firewalls.index')
      ->with('success', 'تم الغاء حظر عنوان هذا IP:'. $ip->ip . ' بنجاح');
  }
}
