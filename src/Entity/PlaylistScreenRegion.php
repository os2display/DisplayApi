<?php

namespace App\Entity;

use App\Repository\PlaylistScreenRegionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaylistScreenRegionRepository::class)
 * @ORM\Table (
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="unique_idx", columns={"playlist_id", "screen_id", "region_id"})
 *     }
 * )
 */
class PlaylistScreenRegion
{
    use EntityIdTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Playlist::class, inversedBy="playlistScreenRegions")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Playlist $playlist;

    /**
     * @ORM\ManyToOne(targetEntity=Screen::class, inversedBy="playlistScreenRegions")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Screen $screen;

    /**
     * @ORM\ManyToOne(targetEntity=Campaign::class, inversedBy="playlistScreenRegions")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Campaign $campaign;

    /**
     * @ORM\ManyToOne(targetEntity=ScreenLayoutRegions::class, inversedBy="playlistScreenRegions")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ScreenLayoutRegions $region;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private int $weight = 0;

    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(Playlist $playlist): self
    {
        $this->playlist = $playlist;

        return $this;
    }

    public function removePlaylist(): self
    {
        $this->playlist = null;

        return $this;
    }

    public function getScreen(): ?Screen
    {
        return $this->screen;
    }

    public function setScreen(Screen $screen): self
    {
        $this->screen = $screen;

        return $this;
    }

    public function removeScreen(): self
    {
        $this->screen = null;

        return $this;
    }

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(Campaign $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function removeCampaign(): self
    {
        $this->campaign = null;

        return $this;
    }

    public function getRegion(): ?ScreenLayoutRegions
    {
        return $this->region;
    }

    public function setRegion(ScreenLayoutRegions $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function removeRegion(): self
    {
        $this->region = null;

        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
