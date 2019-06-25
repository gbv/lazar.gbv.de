<?php declare(strict_types=1);

namespace GBV\OAI;

/**
 * Persistent storage of queries, indexed by resumption tokens.
 */
class TokenFile
{

    public function __construct($file = null, $ttl = 3600)
    {
        $this->file = $file ? $file : sys_get_temp_dir() .  '/oai-proxy-tokens.tsv';
        $this->ttl = $ttl;
    }

    public function get(string $token)
    {
        foreach ($this->lines() as $line) {
            list ($time, $tk, $query) = explode("\t", $line);
            if ($token === $tk) {
                return json_decode($query, true);
            }
        }
    }

    public function add(string $token, array $query)
    {
        $tokens = iterator_to_array($this->lines());
        array_unshift($tokens, implode("\t", [ time(), $token, json_encode($query) ]));
        file_put_contents($this->file, implode("\n", $tokens));
    }

    function lines()
    {
        if (file_exists($this->file)) {
            foreach (file($this->file) as $line) {
                list ($time) = explode("\t", $line);
                if ((time() - $time) > $this->ttl) {
                    break;
                }
                yield $line;
            }
        }
    }
}
