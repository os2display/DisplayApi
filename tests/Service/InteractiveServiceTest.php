<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Tenant\Slide;
use App\Exceptions\InteractiveSlideException;
use App\InteractiveSlide\InstantBook;
use App\InteractiveSlide\InteractionSlideRequest;
use App\Repository\UserRepository;
use App\Service\InteractiveSlideService;
use Doctrine\ORM\EntityManager;
use Hautelook\AliceBundle\PhpUnit\BaseDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InteractiveServiceTest extends KernelTestCase
{
    use BaseDatabaseTrait;

    private EntityManager $entityManager;
    private ContainerInterface $container;

    public static function setUpBeforeClass(): void
    {
        static::bootKernel();
        static::ensureKernelTestCase();
        if (!filter_var(getenv('API_TEST_CASE_DO_NOT_POPULATE_DATABASE'), FILTER_VALIDATE_BOOL)) {
            static::populateDatabase();
        }
    }

    public function setUp(): void
    {
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
    }

    public function testParseRequestBody(): void
    {
        $interactiveService = $this->container->get(InteractiveSlideService::class);

        $this->expectException(InteractiveSlideException::class);

        $interactiveService->parseRequestBody([
            'test' => 'test',
        ]);

        $interactionRequest = $interactiveService->parseRequestBody([
            'implementationClass' => InstantBook::class,
            'action' => 'test',
            'data' => [],
        ]);

        $correctReturnType = $interactionRequest instanceof InteractionSlideRequest;

        $this->assertTrue($correctReturnType);
    }

    public function testPerformAction(): void
    {
        $interactiveService = $this->container->get(InteractiveSlideService::class);
        $user = $this->container->get(UserRepository::class)->findOneBy(['email' => 'admin@example.com']);

        $this->assertNotNull($user);

        $slide = new Slide();

        $interactionRequest = $interactiveService->parseRequestBody([
            'implementationClass' => InstantBook::class,
            'action' => 'ACTION_NOT_EXIST',
            'data' => [],
        ]);

        $this->expectException(InteractiveSlideException::class);
        $this->expectExceptionMessage('Interactive slide not found');

        $tenant = $user->getActiveTenant();

        $interactiveService->performAction($user, $slide, $interactionRequest);

        $interactiveService->saveConfiguration($tenant, InstantBook::class, []);

        $this->expectException(InteractiveSlideException::class);
        $this->expectExceptionMessage('Action not allowed');

        $interactiveService->performAction($user, $slide, $interactionRequest);
    }

    public function testGetConfigurables(): void
    {
        $interactiveService = $this->container->get(InteractiveSlideService::class);

        $this->assertCount(1, $interactiveService->getConfigurables());
    }

    public function testGetImplementation(): void
    {
        $interactiveService = $this->container->get(InteractiveSlideService::class);

        $service = $interactiveService->getImplementation(InstantBook::class);

        $instanceOf = $service instanceof InstantBook;

        $this->assertTrue($instanceOf);
    }
}
