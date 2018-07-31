<?php

namespace Tests\Unit\Entities;

use App\Entities\Task;
use Tests\TestCase;
use \DateTime;

class TaskTest extends TestCase
{
    const ID = 1;
    const TITLE = 'TaskListTitle';
    const DESCRIPTION = 'some description';
    const STATUS = 'statusTest';

    /**
     * Test for get and set id
     * @return void
     */
    public function testSetGetId()
    {

        $task = new Task();

        $testTask = $task->setId(self::ID);
        $this->assertAttributeEquals(self::ID, 'id', $task);
        $this->assertInstanceOf(Task::class, $testTask);
        $taskId = $task->getId();
        $this->assertEquals(self::ID, $taskId);
    }

    /**
     * Test for get and set title
     *
     * @return void
     */
    public function testGetSetTitle()
    {
        $task = new Task();

        $testTask = $task->setTitle(self::TITLE);
        $this->assertAttributeEquals(self::TITLE, 'title', $task);
        $this->assertInstanceOf(Task::class, $testTask);
        $title = $task->getTitle();
        $this->assertEquals(self::TITLE, $title);
    }

    /**
     * Test for get and set description
     *
     * @return void
     */
    public function testGetSetDescription()
    {
        $task = new Task();

        $testTask = $task->setDescription(self::DESCRIPTION);
        $this->assertAttributeEquals(self::DESCRIPTION, 'description', $task);
        $this->assertInstanceOf(Task::class, $testTask);
        $description = $task->getDescription();
        $this->assertEquals(self::DESCRIPTION, $description);
    }

    /**
     * Test for get and set creation date
     *
     * @return void
     */
    public function testSetGetCreationDate()
    {
        $task = new Task();
        $date = new DateTime();

        $testDate = $task->setCreationDate($date);
        $this->assertAttributeEquals($date, 'creationDate', $task);
        $this-> assertInstanceOf(Task::class, $testDate);
        $creationDate = $task->getCreationDate();
        $this->assertEquals($date, $creationDate);
    }

    /**
     * Test for get and set status
     * 
     * @return void
     */
    public function testSetGetStatus()
    {
        $task = new Task();

        $testStatus = $task->setStatus(self::STATUS);
        $this->assertAttributeEquals(self::STATUS, 'status', $task);
        $this->assertInstanceOf(Task::class, $testStatus);
        $status = $task->getStatus();
        $this->assertEquals(self::STATUS, $status);
    }
}
