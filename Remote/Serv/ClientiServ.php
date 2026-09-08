<?php

class ClientiService {

    private ClientiRepository $repository;

    public function __construct(ClientiRepository $repository) {
        $this->repository = $repository;
    }

    public function ottieniClienti(): string {

        $clienti = $this->repository->trovaTutti();

        return json_encode($clienti);
    }
}