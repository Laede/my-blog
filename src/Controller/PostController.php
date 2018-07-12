<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Labels;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\ImageRepository;
use App\Service\ImageResizer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @Route("/post/new", name="post_new")
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $post = new Post();
        $post->setAuthor($user);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('post/new.html.twig', [
            'user' => $user,
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/images", name="uploadImage")
     */
    public function uploadImage(Request $request, LoggerInterface $logger, ImageRepository $imageRepository)
    {
        $file=$request->files->get('upload');
        /** @var UploadedFile $file */
        $filename = $this->generateUniqueFileName().'.'.$file->guessClientExtension();

        $file->move(
            $this->getParameter("uploaded_images_directory"),
            $filename
        );

        $em = $this->getDoctrine()->getManager();
        $imageInDB = new Image();
        $imageInDB->setBody($filename);
        $em->persist($imageInDB);
        $em->flush();

        $image_url = '/uploads/images/'.$filename;
        $this->resizeImage($this->getParameter('uploaded_images_directory').'/'.$filename,700);
        return new JsonResponse(array(
            'uploaded'=>true,
            'url'=>$image_url,
        ));
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    public function resizeImage($imageURL, $maxWidth){
        $resizedIMG = new ImageResizer($imageURL);
        $imageWidth = $resizedIMG->getWidth();
        if ($imageWidth<$maxWidth){
            $resizedIMG->saveImage($imageURL);
        }else{
            $resizedIMG->resizeImage($maxWidth, 0, 'landscape');
            $resizedIMG->saveImage($imageURL);
        }
        return;
    }

    /**
     * @Route("/post/{id}", name="post_show", methods="GET")
     */
    public function show(Post $post): Response
    {
        $user = $this->getUser();
        return $this->render('post/show.html.twig', [
            'user' => $user,
            'post' => $post
        ]);
    }

    /**
     * @Route("/post/{id}/edit", name="post_edit", methods="GET|POST")
     */
    public function edit(Request $request, Post $post): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'user' => $user,
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_delete", methods="DELETE")
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}