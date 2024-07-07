<?php
namespace App\Controller;

use App\Entity\ExerciseLog;
use App\Form\Type\EditExerciseLogType;
use App\Form\Type\ExerciseLogType;
use App\Repository\ExerciseLogRepository;
use App\Repository\ExerciseRepository;
use App\Repository\WorkoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExerciseLogController extends AbstractController
{
    #[Route('/exercise-log/{id}/new', name: 'new-exercise-log', methods: ['GET'])]
    public function new(int $id, ExerciseRepository $exerciseRepository): Response
    {
        $exerciseLog = new ExerciseLog();
        $exercise = $exerciseRepository->find($id);
        if (!$exercise) {
            throw $this->createNotFoundException('The exercise does not exist');
        }

        $form = $this->createForm(ExerciseLogType::class, $exerciseLog,[
            'action' => $this->generateUrl('create-exercise-log', ['exerciseId' => $exercise->getId()]),
            'method'=>'POST'
        ]);

        return $this->render('exercise_log/addExerciseLog.html.twig', [
            'form' => $form->createView(),
            'exercise' => $exercise,
        ]);
    }

    #[Route('/exercise-log/{exerciseId}', name: 'create-exercise-log', methods: ['POST'])]
    public function create(int $exerciseId, Request $request, EntityManagerInterface $entityManager, ExerciseRepository $exerciseRepository): Response
    {
        $exerciseLog = new ExerciseLog();
        $exercise = $exerciseRepository->find($exerciseId);
        if (!$exercise) {
            throw $this->createNotFoundException('The exercise does not exist');
        }

        $form = $this->createForm(ExerciseLogType::class, $exerciseLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exerciseLog = $form->getData();
            $exerciseLog->setExercise($exercise);
            $exerciseLog->setStartTime(new \DateTime());
            $entityManager->persist($exerciseLog);
            $entityManager->flush();

            $this->addFlash('success', 'Exercise added successfully to the workout!');

            return $this->redirectToRoute('show-exercises');
        }

        return $this->render('exercise_log/addExerciseLog.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    #[Route('exercise-logs/{workoutId}', name: 'show-exercise-logs', methods: ['GET'])]
    public function index(int $workoutId, ExerciseLogRepository $exerciseLogRepository): Response
    {
        $exercises = $exerciseLogRepository->findByWorkoutId($workoutId);

        return $this->render('exercise_log/showExerciseLog.html.twig', [
            'exerciseLogs' => $exercises,
        ]);
    }

    #[Route('/exercise-logs/{workoutId}/{exerciseId}/edit', name: 'edit-exercise-log', methods: ['GET'])]
    public function edit(int $workoutId, int $exerciseId, ExerciseLogRepository $exerciseLogRepository): Response
    {
        $exerciseLog = $exerciseLogRepository->findByWorkoutAndUser($workoutId, $exerciseId);

        $form = $this->createForm(EditExerciseLogType::class, $exerciseLog,[
            'action' => $this->generateUrl('update-exercise-log', ['workoutId'=>$workoutId, 'exerciseId'=>$exerciseId]),
            'method' => 'PATCH',]);
        return $this->render('exercise_log/editExerciseLog.html.twig', [
            'form' => $form,
            'exerciseLog' => $exerciseLog,
        ]);
    }

    #[Route('/workouts/{workoutId}/{exerciseId}', name: 'update-exercise-log', methods: ['PATCH'])]
    public function update(int $workoutId, int $exerciseId, EntityManagerInterface $entityManager, ExerciseLogRepository $exerciseLogRepository, Request $request): Response
    {
        $exerciseLog = $exerciseLogRepository->findByWorkoutAndUser($workoutId, $exerciseId);

        $form = $this->createForm(EditExerciseLogType::class, $exerciseLog, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Exercise Log Edited!');
            return $this->redirectToRoute('show-exercise-logs', ['workoutId'=>$workoutId]);
        }
        return $this->render('exercise_log/editExerciseLog.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/exercise-logs/{workoutId}/{exerciseId}', name: 'delete-exercise-log', methods: ['GET','DELETE'])]
    public function delete(int $workoutId, int $exerciseId, EntityManagerInterface $entityManager,  ExerciseLogRepository $exerciseLogRepository): Response
    {
        $exerciseLog = $exerciseLogRepository->findByWorkoutAndUser($workoutId, $exerciseId);
        $entityManager->remove($exerciseLog);
        $entityManager->flush();
        return $this->redirectToRoute('show-exercise-logs',['workoutId'=>$workoutId]);
    }
}