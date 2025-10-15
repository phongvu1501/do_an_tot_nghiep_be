<?php
// Lightweight, conditional stubs for static analysis only.
// These are defined only if the real classes/traits are not yet available.

// Note: these stubs are harmless at runtime because they are only defined
// if the real classes/traits do not exist. They help static analyzers.

if (!trait_exists('Laravel\\Sanctum\\HasApiTokens')) {
    namespace Laravel\Sanctum {
        trait HasApiTokens
        {
            public function tokens() {}
            public function currentAccessToken() {}
            public function createToken($name, $abilities = []) {}
        }
    }
}

if (!class_exists('Laravel\\Sanctum\\Sanctum')) {
    namespace Laravel\Sanctum {
        class Sanctum
        {
            public static $personalAccessTokenModel = '\\Laravel\\Sanctum\\PersonalAccessToken';
        }
    }
}

if (!class_exists('Laravel\\Sanctum\\NewAccessToken')) {
    namespace Laravel\Sanctum {
        class NewAccessToken
        {
            public function __construct($token, $accessToken) {}
            public function accessToken() {}
            public function plainTextToken() {}
        }
    }
}

if (!class_exists('Laravel\\Sanctum\\PersonalAccessToken')) {
    namespace Laravel\Sanctum {
        class PersonalAccessToken
        {
            public function getKey() {}
        }
    }
}

if (!class_exists('Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful')) {
    namespace Laravel\Sanctum\Http\Middleware {
        class EnsureFrontendRequestsAreStateful
        {
            public function handle($request, $next) { return $next($request); }
        }
    }
}
<?php
// Lightweight, conditional stubs for static analysis only.
// These are defined only if the real classes/traits are not yet available.

if (! trait_exists('\Laravel\Sanctum\HasApiTokens')) {
    namespace Laravel\Sanctum {
        trait HasApiTokens
        {
            // stubbed methods for analyzer
            public function tokens() {}
            public function currentAccessToken() {}
            public function createToken($name, $abilities = []) {}
        }
    }
}

if (! class_exists('\Laravel\Sanctum\Sanctum')) {
    namespace Laravel\Sanctum {
        class Sanctum
        {
            public static $personalAccessTokenModel = '\\Laravel\\Sanctum\\PersonalAccessToken';
        }
    }
}

if (! class_exists('\Laravel\Sanctum\NewAccessToken')) {
    namespace Laravel\Sanctum {
        class NewAccessToken
        {
            public function __construct($token, $accessToken) {}
            public function accessToken() {}
            public function plainTextToken() {}
        }
    }
}

if (! class_exists('\Laravel\Sanctum\PersonalAccessToken')) {
    namespace Laravel\Sanctum {
        class PersonalAccessToken
        {
            public function getKey() {}
        }
    }
}

if (! class_exists('\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful')) {
    namespace Laravel\Sanctum\Http\Middleware {
        class EnsureFrontendRequestsAreStateful
        {
            public function handle($request, $next) { return $next($request); }
        }
    }
}
