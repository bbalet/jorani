<?php

namespace App\DataFixtures;

use App\Entity\LeaveRequest;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class LeaveRequestFixtures implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 500; $i++) {
            $leave = new LeaveRequest();
            $startDate = $faker->dateTimeBetween('-5 years');
            $leave->setStartDate($startDate);
            $leave->setStartDateType(LeaveRequest::MORNING);
            $leave->setEndDate($startDate->add(new \DateInterval('P3D')));
            $leave->setEndDateType(LeaveRequest::MORNING);
            $leave->setCause($faker->sentence(5));
            $leave->setDuration(3);
            $leaveType = $this->getReference('leave_type_' . $faker->numberBetween(1, 6));
            $leave->setType($leaveType);
            $manager->persist($leave);
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
