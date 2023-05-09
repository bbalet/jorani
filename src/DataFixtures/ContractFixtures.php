<?php

namespace App\DataFixtures;

use App\Entity\Contract;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ContractFixtures implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        
        $contracts = array(
            1 => ['Standard', null, null],
            2 => ['Part-time', null, null],
            3 => ['Morning', null, null],
            4 => ['Afternoon', null, null],
            5 => ['France', null, null],
        );

        foreach ($contracts as $key => $value) {
            $contract = new Contract();
            $contract->setName($value[0]);
            $nbLeaveTypes = $faker->numberBetween(1, 4);
            for($j=0; $j<=$nbLeaveTypes; $j++) {
                $leaveType = $this->getReference('leave_type_' . $faker->numberBetween(1, 6));
                $contract->addLeaveType($leaveType);
            }
            $manager->persist($contract);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LeaveTypeFixtures::class
        ];
    }
}
