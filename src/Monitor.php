<?php

declare(strict_types=1);

namespace Xtompie\Monitor;

use JsonSerializable;

class Monitor implements JsonSerializable
{
    public function __construct(
        protected string $name = 'monitor',
        protected bool $stdout = false,
        protected int $frequency = 1,
        protected array $data = [],
        protected ?int $start = null,
        protected ?int $last = null,
    ) {
        $this->start = time();
    }

    public function up(string $property, int $value = 1): void
    {
        if (!isset($this->data[$property])) {
            $this->data[$property] = 0;
        }
        $this->data[$property] = $this->data[$property] + $value;
        $this->update();
    }

    public function down(string $property, int $value = 1): void
    {
        $this->data[$property] = $this->data[$property] - $value;
        $this->update();
    }

    public function set(string $property, mixed $value): void
    {
        $this->data[$property] = $value;
        $this->update();
    }

    public function stdout(bool $stdout): static
    {
        $this->stdout = $stdout;
        return $this;
    }

    public function show(): void
    {
        if ($this->stdout === false) {
            return;
        }

        echo $this->render();
    }

    protected function update(): void
    {
        if ($this->stdout === false) {
            return;
        }

        if ($this->last === null || time() - $this->last > $this->frequency) {
            $this->last = time();
            echo $this->render();
        }
    }

    public function render(): string
    {
        return '#' . $this->name
            . ' | ' . date('Y-m-d H:i:s', $this->start)
            . ' | ' . date('Y-m-d H:i:s')
            . ' | ' . $this->secondsHr(time() - $this->start)
            . ' | ' . $this->sizeHr(memory_get_usage(true))
            . ' | ' . $this->state()
            . "\n"
        ;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function jsonSerialize(): string
    {
        return $this->render();
    }

    protected function state(): string
    {
        return implode(' | ', array_map(fn ($k, $v) => "$k: $v", array_keys($this->data), $this->data));
    }

    protected function sizeHr(int $size): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    protected function secondsHr(int $seconds): string
    {
        $s = intval($seconds);
        return @sprintf('%d:%02d:%02d:%02d', intval($s / 86400), intval($s / 3600) % 24, intval($s / 60) % 60, intval($s % 60));
    }
}
