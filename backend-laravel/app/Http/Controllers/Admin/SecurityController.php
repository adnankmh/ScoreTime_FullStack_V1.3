<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
class SecurityController extends Controller { public function index(){return view('admin.security.index',['logs'=>AuditLog::latest('created_at')->paginate(30)]);} }
