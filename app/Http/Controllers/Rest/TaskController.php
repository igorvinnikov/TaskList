<?php

namespace App\Http\Controllers;

use App\Exceptions\Http400ApiException;
use App\Helpers\ApiValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Entities\Task;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Response;

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

    public function __construct(
        EntityManager $entityManager,
        Request $request,
        ApiValidator $validator
    ) {
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->validator = $validator;
    }

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
     *
     *
     * @param int $id Id of the task
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
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
     * @param \App\Entities\Task $task
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function store(Task $task)
    {
        $data = $this->request->all();

        $this->validator->validate($data);

        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setCreationDate(new \DateTime('now'));
        $task->setStatus($data['status']);

        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @throws \Doctrine\ORM\ORMException
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
     * @param int $id
     * @throws \Doctrine\ORM\ORMException
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
