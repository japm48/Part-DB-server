<?php
/**
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 - 2022 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Entity\Attachments;

use App\Entity\Base\AbstractStructuralDBElement;
use App\Entity\Parameters\AttachmentTypeParameter;
use App\Validator\Constraints\ValidFileFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AttachmentType.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StructuralDBElementRepository")
 * @ORM\Table(name="`attachment_types`", indexes={
 *     @ORM\Index(name="attachment_types_idx_name", columns={"name"}),
 *     @ORM\Index(name="attachment_types_idx_parent_name", columns={"parent_id", "name"}),
 * })
 */
class AttachmentType extends AbstractStructuralDBElement
{
    /**
     * @ORM\OneToMany(targetEntity="AttachmentType", mappedBy="parent", cascade={"persist"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected Collection $children;

    /**
     * @ORM\ManyToOne(targetEntity="AttachmentType", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected ?AbstractStructuralDBElement $parent;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @ValidFileFilter
     */
    protected string $filetype_filter = '';
    /**
     * @var Collection<int, AttachmentTypeAttachment>
     * @ORM\OneToMany(targetEntity="App\Entity\Attachments\AttachmentTypeAttachment", mappedBy="element", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"name" = "ASC"})
     * @Assert\Valid()
     */
    protected Collection $attachments;

    /** @var Collection<int, AttachmentTypeParameter>
     * @ORM\OneToMany(targetEntity="App\Entity\Parameters\AttachmentTypeParameter", mappedBy="element", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"group" = "ASC" ,"name" = "ASC"})
     * @Assert\Valid()
     */
    protected Collection $parameters;

    /**
     * @var Collection<int, Attachment>
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="attachment_type")
     */
    protected $attachments_with_type;

    public function __construct()
    {
        parent::__construct();
        $this->attachments = new ArrayCollection();
        $this->attachments_with_type = new ArrayCollection();
    }

    /**
     * Get all attachments ("Attachment" objects) with this type.
     *
     * @return Collection|Attachment[] all attachments with this type, as a one-dimensional array of Attachments
     *                                 (sorted by their names)
     */
    public function getAttachmentsForType(): Collection
    {
        return $this->attachments_with_type;
    }

    /**
     * Gets a filter, which file types are allowed for attachment files.
     * Must be in the format of <input type=file> accept attribute
     * (See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/file#Unique_file_type_specifiers).
     */
    public function getFiletypeFilter(): string
    {
        return $this->filetype_filter;
    }

    /**
     * Sets the filetype filter pattern.
     *
     * @param string $filetype_filter The new filetype filter
     *
     * @return $this
     */
    public function setFiletypeFilter(string $filetype_filter): self
    {
        $this->filetype_filter = $filetype_filter;

        return $this;
    }
}
