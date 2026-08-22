<?php
namespace App\Services;
use App\Exceptions\BusinessRuleViolation; use App\Models\Customer; use App\Models\Project;
final class CreateProject { public function handle(Customer $customer,string $name):Project { if(!$customer->is_active) throw new BusinessRuleViolation('Projects can only be created for active customers.'); $organization=$customer->organization; if($organization->projects()->count() >= $organization->project_limit) throw new BusinessRuleViolation('The organization project limit has been reached.'); return Project::create(['organization_id'=>$organization->id,'customer_id'=>$customer->id,'name'=>$name]); } }
