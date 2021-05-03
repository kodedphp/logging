<?php

namespace Tests\Koded\Logging\PhpBench;

use Koded\Logging\Log;

/**
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 * @OutputTimeUnit("milliseconds")
 */
abstract class AbstractBench
{
    protected ?Log $log;

    public function setUp(): void
    {
        $this->log = new Log($this->getConfig());
    }

    public function tearDown(): void
    {
        $this->log = null;
    }

    abstract protected function getConfig(): array;

    protected function message(): array
    {
        $messages = [
            ['One morning, when {1} woke from troubled dreams, he found himself {2} in his bed into a {3}.', ['1' => 'Gregor Samsa', '2' => 'transformed', '3' => 'horrible vermin']],
            ['He lay on his {back}, and if he lifted his head a little he could see his brown belly, slightly {state} by arches into stiff sections.', ['back' => 'armour-like back', 'state' => 'domed and divided']],
            ['The bedding was hardly able to cover it and seemed ready to slide off any moment.', ['' => '', '' => '', '' => '']],
            ['His many legs, pitifully thin compared with the size of the rest of him, waved about helplessly as he looked.', ['' => '', '' => '', '' => '']],
            ['"What\'s happened to {0}?" he thought. It wasn\'t a {1}.', ['me', 'dream']],
            ['His room, a proper {room} although a little too small, lay peacefully {position} familiar walls.', ['room' => 'human room', 'position' => 'between its four']],
            ['A collection of textile samples lay spread out on the table - {name} was a travelling salesman - and above it there hung a picture that he had recently cut out of an {object} and housed in a nice, gilded frame.', ['name' => 'Samsa', 'object' => 'illustrated magazine']],
            ['It showed a {someone} who sat upright, raising a {something} that covered {somewhere} towards the viewer. ', ['someone' => 'lady fitted out with a fur hat and fur boa', 'something' => 'heavy fur muff', 'somewhere' => 'the whole of her lower arm']],
        ];

        return $messages[rand(0, 7)];
    }
}
