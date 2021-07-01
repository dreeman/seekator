<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidInputUrlException;
use App\Services\Contracts\CommandService as CommandServiceContract;
use App\Services\Contracts\CreativeRepository as CreativeRepositoryContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{
    protected CreativeRepositoryContract $repository;
    protected CommandServiceContract $commandService;

    /**
     * Create a new controller instance.
     *
     * @param CommandServiceContract $commandService
     * @param CreativeRepositoryContract $repository
     */
    public function __construct(CommandServiceContract $commandService, CreativeRepositoryContract $repository)
    {
        $this->commandService = $commandService;
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function run(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
            'from' => ['nullable', 'regex:/^\d\d:\d\d:\d\d/i'],
            'to' => ['nullable', 'regex:/^\d\d:\d\d:\d\d/i'],
        ]);

        try {
            $this->commandService->startProcessingCommand(
                $request->user()->id,
                $this->getCodeByUrl($request->get('url')),
                $request->get('from'),
                $request->get('to')
            );
        } catch (InvalidInputUrlException $e) {
            return $e->toResponse($request, 422);
        }

        return $this->response([
            'message' => 'Video queued',
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        $this->repository->setUser();
        return $this->response([
            'status' => $this->repository->getReadyStatus(),
            'files' => $this->repository->getCreatives(),
        ]);
    }

    /**
     * @param string $url
     * @return string
     * @throws InvalidInputUrlException
     */
    protected function getCodeByUrl(string $url): string
    {
        $parsed = parse_url($url);
        if (in_array($parsed['host'], ['www.youtube.com', 'youtube.com'])) {
            parse_str($parsed['query'], $query);
            if (!isset($query['v'])) {
                throw new InvalidInputUrlException('Invalid URL');
            }
            $code = $query['v'];
        } elseif (in_array($parsed['host'], ['www.youtu.be', 'youtu.be'])) {
            if (!isset($parsed['path']) || empty($code = trim($parsed['path'], '/'))) {
                throw new InvalidInputUrlException('Invalid URL');
            }
        } else {
            throw new InvalidInputUrlException('Invalid URL');
        }

        return $code;
    }
}
