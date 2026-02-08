<?php

use App\Http\Controllers\ADMIN\AccreditationController;
use App\Http\Controllers\ADMIN\ACREDITATIONCONTROLLER;
use App\Http\Controllers\ADMIN\AdminAcreditationController;
use App\Http\Controllers\ADMIN\AdminTaskForceController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccreditationEvaluationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])
        ->name('users.index');
    Route::get('/users/data', [AdminUserController::class, 'data'])
        ->name('users.data');
    Route::post('/users/{id}/verify', [AdminUserController::class, 'verify'])
     ->name('users.verify');
    Route::delete('/users/{id}/suspend', [AdminUserController::class, 'suspend']);
    Route::get('/task-force', [AdminTaskForceController::class, 'index'])
        ->name('users.taskforce.index');
    Route::get('/task-force/data', [AdminTaskForceController::class, 'data'])
        ->name('taskforce.data');
    Route::get('/taskforce/view/{id}', [AdminTaskForceController::class, 'viewTaskForce'])
        ->name('taskforce.view');


    Route::get('/accreditation', [AdminAcreditationController::class, 'index'])
        ->name('admin.accreditation.index');
    Route::post('/admin/accreditations', [AdminAcreditationController::class, 'store'])
        ->name('admin.accreditations.store');
    Route::get('/admin/accreditations/data', [AdminAcreditationController::class, 'getAccreditations'])
        ->name('admin.accreditations.data');
    Route::post(
        '/admin/accreditations/add-level-programs',
        [AdminAcreditationController::class, 'addLevelWithPrograms']
    )->name('admin.accreditations.addLevelPrograms');
    Route::post(
        '/admin/accreditations/add-program',
        [AdminAcreditationController::class, 'addProgramOnly']
    )->name('admin.accreditations.addProgram');

    Route::get(
        '/admin/accreditations/{infoId}/level/{levelId}/program/{programName}',
        [AdminAcreditationController::class, 'showProgram']
    )->name('admin.accreditations.program');
    Route::post('/programs/{program}/areas/save', [AdminAcreditationController::class, 'saveAreas'])
        ->name('programs.areas.save');
    Route::get(
        '/admin/accreditation/{infoId}/{levelId}/{programId}/areas/{programAreaId}/parameters',
        [AdminAcreditationController::class, 'showParameters']
    )->name('program.areas.parameters');
    Route::post('/program-area/{areaId}/parameters', action: [AdminAcreditationController::class, 'storeParameters'])
        ->name('program-area.parameters.store');

    Route::get(
        '/sub-parameter/{subParameter}/uploads/{infoId}/{levelId}/{programId}/{programAreaId}',
        [AdminAcreditationController::class, 'subParameterUploads']
    )->name('subparam.uploads.index');

    Route::post(
        '/sub-parameter/{subParameter}/uploads/{infoId}/{levelId}/{programId}/{programAreaId}',
        [AdminAcreditationController::class, 'storeSubParameterUploads']
    )->name('subparam.uploads.store');
    Route::post(
        '/admin/areas/assign-users',
        [AdminAcreditationController::class, 'assignUsersToArea']
    )->name('areas.assign.users');

    Route::delete(
        '/subparam/uploads/{upload}',
        [AdminAcreditationController::class, 'destroySubParameterUpload']
    )->name('subparam.uploads.destroy');


    Route::get(
        '/admin/accreditations/{id}/edit',
        [AdminAcreditationController::class, 'edit']
    );

    Route::get(
        '/admin/accreditations/{id}',
        [AdminAcreditationController::class, 'show']
    );

    Route::put(
        '/admin/accreditations/{id}',
        [AdminAcreditationController::class, 'update']
    );


    //INTERNAL ACCESSOR
    
    Route::get(
        '/internal-assessor',
        [AdminAcreditationController::class, 'indexInternalAccessor']
    )->name('internal-accessor.index');

    
    Route::get(
        '/internal-assessor/{accreditation}/{level}/{program}/areas',
        [AdminAcreditationController::class, 'showProgramAreas']
        )->name('internal.accessor.program.areas');
        
        
    Route::get(
        '/evaluations',
        [AccreditationEvaluationController::class, 'index']
        )->name('program.areas.evaluations');
        
    Route::get(
        '/program-areas/{infoId}/{levelId}/{programId}/{programAreaId}/evaluation',
        [AdminAcreditationController::class, 'showAreaEvaluation']
        )->name('program.areas.evaluation');

    Route::get(
        '/evaluations/{evaluation}/area/{area}/summary',
        [AccreditationEvaluationController::class, 'show']
    )->name('program.areas.evaluations.summary');
        
    Route::post(
        '/accreditation-evaluations',
        [AccreditationEvaluationController::class, 'store']
    )->name('accreditation-evaluations.store');

    Route::post(
        '/admin/evaluations/{infoId}/{levelId}/{programId}/{programAreaId}',
        [AccreditationController::class, 'store']
    )->name('area.evaluations.store');
    Route::post(
        '/internal/final-verdict',
        [AccreditationController::class, 'storeFinalVerdict']
    )->name('internal.final.verdict.store');

    // FINAL VERDICT
    Route::get('/', [ArchiveController::class, 'index'])
        ->name('archive.index');

    // Completed accreditations
    Route::get('/completed', [ArchiveController::class, 'completed'])
        ->name('archive.completed');

    // 🗑 Deleted / Withdrawn accreditations
    Route::get('/deleted', [ArchiveController::class, 'deleted'])
        ->name('deleted');
});

require __DIR__ . '/auth.php';
