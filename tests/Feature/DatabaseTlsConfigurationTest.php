<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatabaseTlsConfigurationTest extends TestCase
{
    public function test_postgres_uses_the_bundled_supabase_ca_by_default(): void
    {
        $certificatePath = str_replace('\\', '/', database_path('certificates/supabase-root-2021.crt'));

        $this->assertSame('verify-full', config('database.connections.pgsql.sslmode'));
        $this->assertSame($certificatePath, config('database.connections.pgsql.sslrootcert'));
        $this->assertFileExists($certificatePath);
    }
}
