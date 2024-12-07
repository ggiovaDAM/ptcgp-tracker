<?php

    function startSession(): void {
        if (session_status() == PHP_SESSION_NONE) session_start();
    }

    function rollBackTransaction(PDO $db): void {
        if ($db->inTransaction()) $db->rollBack();
    }