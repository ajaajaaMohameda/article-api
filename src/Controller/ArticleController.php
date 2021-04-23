<?php

namespace App\Controller;

use App\Entity\Article;
use App\Exception\ResourceValidationException;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends AbstractFOSRestController
{
    /**
     * @Get(
     *  path="/articles/{id}",
     *  name="app_article_show",
     *  requirements= {"id"="\d+"}
     * )
     * @View
     */
    public function show(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post(
     *  path="/articles",
     * name="app_article_create"
     * )
     * 
     * @Rest\View(StatusCode=201)
     * 
     * @ParamConverter("article", 
     * 
     * converter="fos_rest.request_body")
     * 
     * options= {
     *  "validator" = { "groups"="Create" }
     * }
     */

    public function create(Article $article, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'the JSON sent contains invalid data. Here are the errors you need to correct: ';

            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $this->view($article, Response::HTTP_CREATED, [
            'location' => $this->generateUrl(
                'app_article_show',
                ['id' => $article->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        ]);
    }


    /**
     * @Rest\Get("/articles", name="app_article_list")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function list(ArticleRepository $articleRepository, ParamFetcherInterface $paramFetcher)
    {

        return $articleRepository->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );
    }
}
