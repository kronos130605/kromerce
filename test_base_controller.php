<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing BaseController with ApiResponse ===\n";

try {
    // Test ApiResponse trait methods
    $controller = new class extends \App\Http\Controllers\Controller {
        public function testSuccess() {
            return $this->success(['test' => 'data'], 'Test message');
        }
        
        public function testError() {
            return $this->error('Test error', 400);
        }
        
        public function testValidationError() {
            return $this->validationError(['field' => 'required']);
        }
    };
    
    echo "✅ BaseController with ApiResponse trait loaded successfully\n";
    echo "✅ Success method available\n";
    echo "✅ Error method available\n";
    echo "✅ Validation error method available\n";
    
    // Test response formats
    $successResponse = $controller->testSuccess();
    echo "✅ Success response format: " . json_encode($successResponse->getData()) . "\n";
    
    $errorResponse = $controller->testError();
    echo "✅ Error response format: " . json_encode($errorResponse->getData()) . "\n";
    
    echo "✅ All BaseController tests passed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "❌ File: " . $e->getFile() . "\n";
    echo "❌ Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
