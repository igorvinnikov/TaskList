<?php

namespace App\Http\Controllers\Rest;

use App\Exceptions\Http400ApiException;
use App\Helpers\ApiValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Entities\Task;
use Doctrine\ORM\EntityManager;
use App\Http\Controllers\Controller;

/**
 * Class TaskController
 *
 * @package App\Http\Controllers\Rest
 */
class TaskController extends Controller
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ApiValidator
     */
    private $validator;

    /**
     * TaskController constructor.
     *
     * @param EntityManager $entityManager Class EntityManager.
     * @param Request       $request       Class Request.
     * @param ApiValidator  $validator     Class ApiValidator.
     */
    public function __construct(
        EntityManager $entityManager,
        Request $request,
        ApiValidator $validator
    ) {
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->validator = $validator;
    }

    /**
     * This method shows all tasks which are in database now.
     *
     * @return JsonResponse Returns Task collection in Json format.
     */
    public function index()
    {
        $tasksArray = [];

        $tasks = $this->entityManager->getRepository(Task::class)->findAll();

        foreach ($tasks as $task) {
            $tasksArray [] = $task->toArray();
        }

        return new JsonResponse($tasksArray);
    }

    /**
     * This method shows a single Task by requested id.
     *
     * @param int $id Id of the task
     *
     * @return JsonResponse Returns single Task in Json format.
     *
     * @throws \Doctrine\ORM\ORMException Base exception class for all ORM exceptions.
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function show(int $id)
    {
        $task = $this->entityManager->find(Task::class, $id);

        $response = $task->toArray();

        return new JsonResponse($response);
    }

    /**
     * This method creates a new task.
     *
     * @param Task $task Instance of entity.
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException Base exception class for all ORM exceptions.
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function store(Task $task)
    {
        $data = $this->request->all();

        $this->validator->validate($data);

        $task->setTitle($data['titleTask']);
        $task->setDescription($data['descriptionTask']);
        $task->setCreationDate(new \DateTime('now'));
        $task->setStatus($data['status']);

        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    /**
     * This method updates single Task by requested id.
     *
     * @param Request $request Request.
     * @param integer $id      Id of the task.
     *
     * @return void
     *
     * @throws Http400ApiException Exception class for Bad Request error.
     * @throws \Doctrine\ORM\ORMException Base exception class for all ORM exceptions.
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function update(Request $request, int $id)
    {
        $task = $this->entityManager->find(Task::class, $id);

        if (!isset($task)) {
            throw new Http400ApiException();
        }

        $validatorResponse = $this->validator->validate($request->all());

        if ($validatorResponse === true) {
            $task->setTitle($request->input('title'));
            $task->setDescription($request->input('description'));
            $task->setCreationDate(new \DateTime('now'));
            $task->setStatus($request->input('status'));

            $this->entityManager->persist($task);
            $this->entityManager->flush();
        }
    }

    /**
     * This method delete single Task by requested id.
     *
     * @param integer $id Id of the Task.
     *
     * @throws \Doctrine\ORM\ORMException Base exception class for all ORM exceptions.
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function destroy(int $id)
    {
        $task = $this->entityManager->find(Task::class, $id);

        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}
