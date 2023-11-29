<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileUploadController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(FileReceiver $receiver, Request $request)
    {
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            return $this->saveFile($save->getFile(), $request);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            'done' => $handler->getPercentageDone(),
        ]);
    }

    protected function saveFile(UploadedFile $file, Request $request)
    {
        $type = $request->input('model_type');
        $id = $request->input('model_id');
        $collection = $request->input('collection');

        // Association with media library (only possbile when there is a model that already exists)
        if ($type && $id) {
            $class = app($type);
            $model = $class::find($id);

            $model
                ->addMedia($file->path())
                ->usingName($file->getClientOriginalName())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection($collection);

            return response()->json([
                'success' => trans('Uploaded successfully'),
            ]);
        }

        $path = Storage::putFileAs('chunks', $file, $file->getClientOriginalName());

        return response()->json([
            'file' => [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
            ],
        ]);
    }
}
