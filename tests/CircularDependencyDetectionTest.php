<?php
declare(strict_types=1);

namespace Habemus\Test;

use Exception;
use Habemus\CircularDependencyDetection;
use Habemus\Exception\CircularDependencyException;
use LogicException;
use RuntimeException;

class CircularDependencyDetectionTest extends TestCase
{
    public function testShouldDetectSimpleCircularDependency()
    {
        $cdd = new CircularDependencyDetection();

        $this->expectException(CircularDependencyException::class);
        $cdd->execute(1, function () use ($cdd) {
             $cdd->execute(1, function () {
             });
        });
    }

    public function testShouldDetectNestedCircularDependency()
    {
        $cdd = new CircularDependencyDetection();
        $this->expectException(CircularDependencyException::class);
        $cdd->execute(1, function () use ($cdd) {
            $cdd->execute(2, function () use ($cdd) {
                $cdd->execute(1, function () {
                });
            });
        });
    }

    public function testShouldDetermineProcessExecution()
    {
        $cdd = new CircularDependencyDetection();
        $cdd->execute(1, function () use ($cdd) {
            $this->assertTrue($cdd->isExecuting(1));
        });
    }

    public function testShouldAcquireIdForCircularDependencyDetection()
    {
        $cdd = new CircularDependencyDetection();
        $this->invokeMethod($cdd, 'acquire', [1]);
        $this->assertTrue($cdd->isExecuting(1));
    }

    public function testShouldReleaseIdForCircularDependencyDetection()
    {
        $cdd = new CircularDependencyDetection();
        $this->invokeMethod($cdd, 'acquire', [1]);
        $this->invokeMethod($cdd, 'release', [1]);
        $this->assertFalse($cdd->isExecuting(1));
    }

    public function testShouldReleaseIdAfterExecution()
    {
        $cdd = new CircularDependencyDetection();
        $cdd->execute(1, function () use ($cdd) {
            $this->assertTrue($cdd->isExecuting(1));
        });
        $this->assertFalse($cdd->isExecuting(1));
    }

    public function testShouldReleaseIdAfterExecutionEvenIfFail()
    {
        $cdd = new CircularDependencyDetection();
        try {
            $cdd->execute(1, function () use ($cdd) {
                throw new LogicException("exception");
            });
        } catch (Exception $exception) {
            $this->assertFalse($cdd->isExecuting(1));
        }
    }

    public function testShouldReleaseIdsAfterExecutionEvenIfFail()
    {
        $cdd = new CircularDependencyDetection();
        try {
            $cdd->execute(1, function () use ($cdd) {
                $cdd->execute(2, function () use ($cdd) {
                    $cdd->execute(3, function () {
                        throw new LogicException("exception");
                    });
                });
            });
        } catch (Exception $exception) {
            $this->assertFalse($cdd->isExecuting(1));
            $this->assertFalse($cdd->isExecuting(2));
            $this->assertFalse($cdd->isExecuting(3));
        }
    }

    public function testShouldCircularDependencyExceptionGetExecutionStack()
    {
        $cdd = new CircularDependencyDetection();
        try {
            $cdd->execute(1, function () use ($cdd) {
                $cdd->execute(2, function () use ($cdd) {
                    $cdd->execute(4, function () use ($cdd) {
                        $cdd->execute(1, function () use ($cdd) {
                        });
                    });
                });
            });
        } catch (CircularDependencyException $circularDependencyException) {
            $this->assertEquals([1, 2, 4], $circularDependencyException->getExecutionStack());
        }
    }
}
