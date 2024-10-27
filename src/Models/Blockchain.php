<?php

namespace Iransh\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

class Blockchain extends Model
{
    protected $fillable = ['index', 'previous_hash', 'timestamp', 'data', 'hash'];

    // Generate hash for this block
    public static function calculateHash($index, $previousHash, $timestamp, $data)
    {
        return hash('sha256', $index . $previousHash . $timestamp . json_encode($data));
    }

    // Create a new block
    public static function createBlock($data)
    {
        $lastBlock = self::latest()->first();
        $index = $lastBlock ? $lastBlock->index + 1 : 0;
        $previousHash = $lastBlock ? $lastBlock->hash : '0';
        $timestamp = now()->timestamp;

        $hash = self::calculateHash($index, $previousHash, $timestamp, $data);

        return self::create([
            'index' => $index,
            'previous_hash' => $previousHash,
            'timestamp' => $timestamp,
            'data' => json_encode($data),
            'hash' => $hash,
        ]);
    }

    // Validate the blockchain integrity
    public static function isChainValid()
    {
        $blocks = self::all();

        foreach ($blocks as $index => $block) {
            if ($index > 0) {
                $previousBlock = $blocks[$index - 1];

                if ($block->previous_hash !== $previousBlock->hash) {
                    return false;
                }

                if ($block->hash !== self::calculateHash($block->index, $block->previous_hash, $block->timestamp, json_decode($block->data))) {
                    return false;
                }
            }
        }

        return true;
    }
}
