<?php

namespace App\Entity;

use App\Repository\TodoListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodoListRepository::class)]
class TodoList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'todoList', targetEntity: Task::class)]
    private Collection $tasks_list;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->tasks_list = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasksList(): Collection
    {
        return $this->tasks_list;
    }

    public function addTasksList(Task $tasksList): self
    {
        if (!$this->tasks_list->contains($tasksList)) {
            $this->tasks_list->add($tasksList);
            $tasksList->setTodoList($this);
        }

        return $this;
    }

    public function removeTasksList(Task $tasksList): self
    {
        if ($this->tasks_list->removeElement($tasksList)) {
            // set the owning side to null (unless already changed)
            if ($tasksList->getTodoList() === $this) {
                $tasksList->setTodoList(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
