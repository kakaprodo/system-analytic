<?php


class A
{
    public $name = "proma";
}


class B
{
    public A $a;

    public function __construct(A $a)
    {
        $this->a = clone $a;
    }

    public function updateA()
    {
        $this->a->name = "Kakaprodo";
    }
}

$a = new A();
$b = new B($a);
$b->updateA();

echo "'$a->name'";
echo $b->a->name;
