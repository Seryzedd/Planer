<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\TchatRoom;
use App\Entity\Message;
use App\Entity\User\User;
use App\Repository\MessageRepository;

#[Route('/tchat')]
class MessagerController extends BaseController
{
    

    #[Route('/Rooms/new', name: 'chat_room_new')]
    public function tchat(Request $request): JsonResponse
    {
        $room = new TchatRoom();

        $room->setName($request->get('title'));
        
        $entityManager = $this->entityManager;
        foreach($request->request as $paramName => $value) {
            if (str_starts_with($paramName, 'user_')) {
                $user = $entityManager->getRepository(User::class)->find(str_replace('user_', '', $value));
                
                if ($user) {
                    $room->addTitle($user);

                    
                    $entityManager->persist($user);
                    
                }
            }
        }

        $room->addTitle($this->getUser());

        $entityManager->persist($room);

        $rooms = $entityManager->getRepository(TchatRoom::class)->findAll();
        
        $entityManager->flush();

        return new JsonResponse(['title' => $room->getName(), 'users' => $room->getTitle()]);
    }

    #[Route('/room/{id}/update', name: 'chat_room_update')]
    public function roomUpdate(TchatRoom $room, Request $request): JsonResponse
    {
        $room->setName($request->get('name'));

        $entityManager = $this->entityManager;
        foreach($request->request as $paramName => $value) {
            if (str_starts_with($paramName, 'user_')) {
                $user = $entityManager->getRepository(User::class)->find(str_replace('user_', '', $value));
                
                if ($user) {
                    $room->addTitle($user);

                    
                    $entityManager->persist($user);
                    
                }
            }
        }

        foreach($room->getTitle() as $user) {
            if (!$request->get('user_' . $user->getId()) && $user->getId() !== $this->getUser()->getId()) {
                $room->removeTitle($user);
            }
        }

        $entityManager->persist($room);
        
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/room/get/{id}', name: 'chat_room_get')]
    public function showRoom(TchatRoom $tchatroom): JsonResponse
    {
        $entityManager = $this->entityManager;
        
        foreach($tchatroom->getMessages() as $chatMessage)
        {
            $chatMessage->addReadedUsersId($this->getUser());
            $entityManager->persist($chatMessage);
        }

        $entityManager->flush();

        return new JsonResponse([
            'connectedId' => $this->getUser()->getId(),
            'room' => $tchatroom->toArray(),
            'messageUrl' => $this->generateUrl('chat_room_message',
                [
                    'id' => $tchatroom->getId()]),
                    'messagesUpdateUrl' => $this->generateUrl('chat_room_get', ['id' => $tchatroom->getId()
                    ])
                ]
            );
    }

    #[Route('/Room/message/{id}', name: 'chat_room_message')]
    public function newMessageRoom(TchatRoom $tchatroom, Request $request): JsonResponse
    {
        $message = new Message();
        $this->getUser()->addMessage($message);
        $message->setAuthor($this->getUser());
        $message->setContent($request->get('text'));
        $tchatroom->addMessage($message);

        foreach($tchatroom->getMessages() as $chatMessage)
        {
            $chatMessage->addReadedUsersId($this->getUser());
            $entityManager->persist($chatMessage);
        }

        $entityManager = $this->entityManager;

        $entityManager->persist($this->getUser());
        $entityManager->persist($message);
        $entityManager->persist($tchatroom);
        $entityManager->flush();

        return new JsonResponse($message->toArray());
    }

    #[Route('/messages/count', name: 'chat_room_messages_count')]
    public function countMessages(MessageRepository $messageRepository): JsonResponse
    {
        $messages = [];
        foreach($messageRepository->getMessages($this->getUser()) as $message) {
            if (!$message->isUserReaded($this->getUser())) {
                if (!isset($messages[$message->getRoom()->getId()])) {
                    $messages[$message->getRoom()->getId()][] = $message->toArray();
                } else {
                    $messages[$message->getRoom()->getId()][] = $message->toArray();
                }
            }
            
        }

        return new JsonResponse($messages);
    }


}