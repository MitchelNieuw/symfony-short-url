<?php

namespace App\Controller;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package App\Controller
 */
class UrlController extends AbstractController
{
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('url/index.html.twig', [
            'urls' => $this->getDoctrine()->getRepository(Url::class)->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function url(Request $request)
    {
        if ($this->isCsrfTokenValid('create-item', $request->get('token'))) {
            $url = new Url();
            $url->setUrl($request->get('url'));
            $url->setShortUrl($request->getScheme() . '://' . $request->getHttpHost() . '/'
                .strtotime('now') . random_int(1, 99));

            $this->em->persist($url);
            $this->em->flush();
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @param Request $request
     * @param $randomString
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function find(Request $request, $randomString)
    {
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . '/' . $randomString;

        $shortUrl = $this->getDoctrine()->getRepository(Url::class)
                        ->findOneBy(['short_url' => $baseUrl]);

        return $this->redirect($shortUrl->getUrl());
    }
}