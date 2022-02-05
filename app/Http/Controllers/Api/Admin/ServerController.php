<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\DeleteServer;
use App\Jobs\SuspendServer;
use App\Jobs\UnsuspendServer;
use App\Models\Server;

class ServerController extends ApiController
{
    public function suspend($id)
    {
        $server = Server::find($id);

        if ($server->status === 2) return $this->respondJson(['error' => 'The server has already been suspended!']);

        $server->status = 2;
        $server->save();

        SuspendServer::dispatch($id);

        return $this->respondJson(['success' => 'The server is going to be suspended.']);
    }

    public function unsuspend($id)
    {
        $server = Server::find($id);

        if ($server->status !== 2) return $this->respondJson(['error' => 'The server is not suspended!']);
        
        $server->status = 0;
        $server->save();

        UnsuspendServer::dispatch($id);

        return $this->respondJson(['success' => 'The server is going to be unsuspended.']);
    }

    public function delete($id)
    {
        $server = Server::find($id);

        if ($server->status === 1) return $this->respondJson(['error' => 'Actions cannot be taken on pending servers!']);
        if ($server->status === 3) return $this->respondJson(['error' => 'The server has already been deleted!']);

        $server->status = 3;
        $server->save();

        DeleteServer::dispatch($id);

        return $this->respondJson(['success' => 'The server is going to be deleted.']);
    }
}
