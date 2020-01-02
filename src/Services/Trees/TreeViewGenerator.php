<?php
/**
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace App\Services\Trees;


use App\Entity\Base\DBElement;
use App\Entity\Base\StructuralDBElement;
use App\Helpers\Trees\TreeViewNodeIterator;
use App\Helpers\TreeViewNode;
use App\Repository\StructuralDBElementRepository;
use App\Services\EntityURLGenerator;
use App\Services\UserCacheKeyGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TreeViewGenerator
{

    protected $urlGenerator;
    protected $em;
    protected $cache;
    protected $keyGenerator;

    public function __construct(EntityURLGenerator $URLGenerator, EntityManagerInterface $em,
        TagAwareCacheInterface $treeCache, UserCacheKeyGenerator $keyGenerator)
    {
        $this->urlGenerator = $URLGenerator;
        $this->em = $em;
        $this->cache = $treeCache;
        $this->keyGenerator = $keyGenerator;
    }

    public function getTreeView(string $class, ?StructuralDBElement $parent = null, string $href_type = 'list_parts', DBElement $selectedElement = null)
    {
        $generic = $this->getGenericTree($class, $parent);
        $treeIterator = new TreeViewNodeIterator($generic);
        $recursiveIterator = new \RecursiveIteratorIterator($treeIterator);
        foreach ($recursiveIterator as $item) {
            /** @var $item TreeViewNode */
            if ($selectedElement !== null && $item->getId() === $selectedElement->getID()) {
               $item->setSelected(true);
            }

            if (!empty($item->getNodes())) {
                $item->addTag((string) \count($item->getNodes()));
            }

            if (!empty($href_type)) {
                $entity = $this->em->getPartialReference($class, $item->getId());
                $item->setHref($this->urlGenerator->getURL($entity, $href_type));
            }
        }

        return $generic;
    }

    /**
     * /**
     * Gets a tree of TreeViewNode elements. The root elements has $parent as parent.
     * The treeview is generic, that means the href are null and ID values are set.
     *
     * @param  string  $class The class for which the tree should be generated
     * @param  StructuralDBElement|null  $parent The parent the root elements should have.
     * @return TreeViewNode[]
     */
    public function getGenericTree(string $class, ?StructuralDBElement $parent = null) : array
    {
        if(!is_a($class, StructuralDBElement::class, true)) {
            throw new \InvalidArgumentException('$class must be a class string that implements StructuralDBElement!');
        }
        if($parent !== null && !is_a($parent, $class)) {
            throw new \InvalidArgumentException('$parent must be of the type class!');
        }

        /** @var StructuralDBElementRepository $repo */
        $repo = $this->em->getRepository($class);

        //If we just want a part of a tree, dont cache it
        if ($parent !== null) {
           return $repo->getGenericNodeTree($parent);
        }

        $secure_class_name = str_replace('\\', '_', $class);
        $key = 'treeview_'.$this->keyGenerator->generateKey().'_'.$secure_class_name;

        $ret = $this->cache->get($key, function (ItemInterface $item) use ($repo, $parent, $secure_class_name) {
            // Invalidate when groups, a element with the class or the user changes
            $item->tag(['groups', 'tree_treeview', $this->keyGenerator->generateKey(), $secure_class_name]);
            return $repo->getGenericNodeTree($parent);
        });

        return $ret;
    }
}