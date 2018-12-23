<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * Class BlogController
 *
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @Route("/{page}", defaults={"page": 5}, requirements={"page"="\d+"})
     * @param int     $page
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function list($page = 1, Request $request)
    {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return $this->json([
            'page' => $page,
            'limit' => $limit,
            'data' =>array_map(function ( BlogPost $item) {
                return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
            }, $items)
        ]);
    }

    /**
     * @Route("/post/{id}", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("posts", class="App:BlogPost")
     * @param BlogPost $post
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @ParamConverter("post", class="App:BlogPost" , options={"mapping": {"slug": "slug"}})
     *
     * @param BlogPost $post
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postBySlug($post)
    {
        return $this->json($post);

    }

    /**
     * @Route("/add", methods={"POST"})
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/post/{id}", methods={"DELETE"})
     * @param BlogPost $blogPost
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(BlogPost $blogPost)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);

    }
}