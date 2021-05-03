<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $company_name;

    /**
     * @ORM\OneToMany(targetEntity=GasReading::class, mappedBy="company_id")
     */
    private $gasReadings;

    public function __construct()
    {
        $this->gasReadings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(string $company_name): self
    {
        $this->company_name = $company_name;

        return $this;
    }

    /**
     * @return Collection|GasReading[]
     */
    public function getGasReadings(): Collection
    {
        return $this->gasReadings;
    }

    public function addGasReading(GasReading $gasReading): self
    {
        if (!$this->gasReadings->contains($gasReading)) {
            $this->gasReadings[] = $gasReading;
            $gasReading->setCompany($this);
        }

        return $this;
    }

    public function removeGasReading(GasReading $gasReading): self
    {
        if ($this->gasReadings->removeElement($gasReading)) {
            // set the owning side to null (unless already changed)
            if ($gasReading->getCompany() === $this) {
                $gasReading->setCompany(null);
            }
        }

        return $this;
    }
}
