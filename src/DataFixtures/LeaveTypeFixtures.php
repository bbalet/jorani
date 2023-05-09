<?php

namespace App\DataFixtures;

use App\Entity\LeaveType;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LeaveTypeFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $types = array(
            1 => ['Paid Leave', 'PL', false],
            2 => ['Maternity Leave', 'MaL', true],
            3 => ['Paternity Leave', 'PaL', true],
            4 => ['Special Leave', 'SpL', false],
            5 => ['Sick Leave', 'Sick', false],
            6 => ['RTT', 'RTT', false],
        );

        foreach ($types as $key => $value) {
            $type = new LeaveType();
            $type->setName($value[0]);
            $type->setAcronym($value[1]);
            $type->setDeductDaysOff($value[2]);
            $manager->persist($type);
            $this->addReference('leave_type_'.$key, $type);
        }

        $manager->flush();
    }
}
