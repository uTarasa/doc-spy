<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\Item;
use CoreBundle\Entity\Tag;
use CoreBundle\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $itemsRepository = $this->getDoctrine()->getRepository(Item::class);
        $items = $itemsRepository->findFromFavoriteSources();

        return $this->render('CoreBundle:Default:index.html.twig', ['items' => $items]);
    }
}
