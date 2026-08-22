<?php
namespace Tests;
use App\Models\Membership;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
abstract class TestCase extends BaseTestCase {
 /** @return array{User, Organization} Deterministic, explicit tenant test data. */
 protected function member(string $role='agent'):array {
  $organization=Organization::create(['name'=>'Test organization']);
  $user=User::create(['name'=>'Test user','email'=>uniqid('member-',true).'@test.test','password'=>'password']);
  Membership::create(['user_id'=>$user->id,'organization_id'=>$organization->id,'role'=>$role]);
  return [$user,$organization];
 }
}
