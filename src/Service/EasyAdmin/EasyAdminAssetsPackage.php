<?php

declare(strict_types=1);

namespace App\Service\EasyAdmin;

use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\HttpFoundation\RequestStack;

class EasyAdminAssetsPackage implements PackageInterface
{
    private PackageInterface $package;

    public function __construct(RequestStack $requestStack)
    {
        $baseUrl = '/public';
        if (!is_null($requestStack->getCurrentRequest())) {
            $baseUrl = $requestStack->getCurrentRequest()->server->getString('SERVER_PREFIX_URL') . '/public';
        }

        $this->package = new PathPackage(
            $baseUrl
            . '/bundles/easyadmin',
            new JsonManifestVersionStrategy(__DIR__ . '/../../../public/bundles/easyadmin/manifest.json'),
            new RequestStackContext($requestStack)
        );
    }

    public function getUrl(string $path): string
    {
        return $this->package->getUrl($path);
    }

    public function getVersion(string $path): string
    {
        return $this->package->getVersion($path);
    }
}
