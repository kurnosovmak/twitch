<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class HomeController
{
    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private Environment $twig,
        private EntityManagerInterface $em
    ) {}

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = isset($request->getQueryParams()['id'])?$request->getQueryParams()['id']:null;
        if(isset($id)){
            return $this->one($request,$response);
        }

        try {
            $data = $this->twig->render('home/index.html.twig', [
                'trailers' => $this->fetchData(),
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }


    public function one(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{
        try {
            $id = isset($request->getQueryParams()['id'])?$request->getQueryParams()['id']:null;
//
            $trailer = $this->fetchData();
            $data = ($trailer->get($id));

            $data = $this->twig->render('home/one.html.twig', [
                'trailer' => $data,
            ]);

//            $data = json_encode($data);

        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    protected function fetchData(): Collection
    {
        $data = $this->em->getRepository(Movie::class)
            ->findAll();

        return new ArrayCollection($data);
    }

    protected function getMovieById(int $id)
    {
//        $data = $this->em->getRepository(Movie::class)
//            ->find($id);

        $repository = $this->em->getRepository(Movie::class);

        $product = $repository->find($id);

        return $product;
    }
}
