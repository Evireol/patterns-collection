<?php

class Task
{
    public function __construct(public string $title, public bool $completed = false) {}
}

class TaskCollection implements IteratorAggregate
{
    private array $tasks = [];

    public function add(Task $task): void
    {
        $this->tasks[] = $task;
    }

    // Возвращаем внутренний итератор массива
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tasks);
    }

    // Можно добавить кастомный итератор для фильтрации
    public function getCompletedIterator(): Traversable
    {
        return new CallbackFilterIterator(
            new ArrayIterator($this->tasks),
            fn(Task $task) => $task->completed
        );
    }
}

// Использование
$tasks = new TaskCollection();
$tasks->add(new Task("Купить молоко"));
$tasks->add(new Task("Сделать ДЗ", completed: true));
$tasks->add(new Task("Погулять с собакой", completed: true));

echo "Все задачи:\n";
foreach ($tasks as $task) {
    echo "- {$task->title} (" . ($task->completed ? '✓' : ' ') . ")\n";
}

echo "\nВыполненные:\n";
foreach ($tasks->getCompletedIterator() as $task) {
    echo "- {$task->title}\n";
}