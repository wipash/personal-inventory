<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Tag as TagDocument;

use App\Service\ImageStorage;

class Tag extends Controller
{

    /** @var ImageStorage */
    protected $images;

    public function __construct(DocumentManager $dm, ImageStorage $images)
    {
        $this->dm = $dm;
        $this->tagRepo = $this->dm->getRepository(TagDocument::class);
        $this->images = $images;
    }

    /**
     * Render list of tags from a category
     *
     * @param string $category One of Tag::CATEGORY_*
     */
    public function listTags(string $category)
    {
        $tags = $this->tagRepo->findAllByCategory($category);
        $images = [];
        // foreach ($tags as $tag) {
        //     // Get a random image associated with the tag
        //     $images[$tag->getName()] = null;
        //     $item = $this->docs->getRandomInventoryItemByTag($category, $tag->getName());
        //     if ($item) {
        //         $itemImages = $this->images->getItemImages($item);
        //         if ($count = count($itemImages)) {
        //             $rand = rand(0, $count - 1);
        //             $images[$tag->getName()] = ['itemid' => $item->getId(), 'filename' => $itemImages[$rand]];
        //         }
        //     }
        // }
        return $this->render('tag/list.html.twig', ['tags' => $tags, 'images' => $images]);
    }
}
