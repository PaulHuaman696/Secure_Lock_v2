<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Area;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Alumno;
use App\Models\CursoHorario;
use App\Models\CursoDocente;
use App\Models\CursoArea;
use App\Models\Matricula;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    // Listar Horarios
    public function index()
    {
        $horarios = Horario::with(['horarioPersonalLimpieza'])->get();
        return $horarios;
    }

    // Ver un Horario
    public function show($id)
    {
        $horario = Horario::with(['horarioPersonalLimpieza'])->find($id);
        if (is_null($horario)) {
            return 'El horario buscado no existe.';
        }
        return $horario;
    }

    // Crear un Horario
    public function store(Request $request)
    {
        $params = $request->all();
        $horario = Horario::create([
            'dia' => $params['dia'],
            'hora_inicio' => $params['hora_inicio'],
            'hora_fin' => $params['hora_fin']
        ]);

        // Asociar Áreas
        if (isset($params['areas']) && is_array($params['areas'])) {
            foreach ($params['areas'] as $areaId) {
                $area = Area::find($areaId);
                if ($area) {
                    CursoArea::create([
                        'id_curso' => $params['curso_id'], // Asumiendo que 'curso_id' es proporcionado
                        'id_area' => $areaId
                    ]);
                }
            }
        }

        // Asociar Cursos
        if (isset($params['cursos']) && is_array($params['cursos'])) {
            foreach ($params['cursos'] as $cursoId) {
                $curso = Curso::find($cursoId);
                if ($curso) {
                    CursoHorario::create([
                        'id_curso' => $cursoId,
                        'id_horario' => $horario->id
                    ]);
                }
            }
        }

        // Asociar Docentes
        if (isset($params['docentes']) && is_array($params['docentes'])) {
            foreach ($params['docentes'] as $docenteId) {
                $docente = Docente::find($docenteId);
                if ($docente) {
                    CursoDocente::create([
                        'id_curso' => $params['curso_id'], // Asumiendo que 'curso_id' es proporcionado
                        'id_docente' => $docenteId
                    ]);
                }
            }
        }

        // Asociar Alumnos
        if (isset($params['alumnos']) && is_array($params['alumnos'])) {
            foreach ($params['alumnos'] as $alumnoId) {
                $alumno = Alumno::find($alumnoId);
                if ($alumno) {
                    Matricula::create([
                        'id_curso' => $params['curso_id'], // Asumiendo que 'curso_id' es proporcionado
                        'id_alumno' => $alumnoId
                    ]);
                }
            }
        }

        return response()->json($horario, 201);
    }

    // Eliminar Horario
    public function destroy($id)
    {
        $horario = Horario::find($id)->delete();

        if ($horario) {
            return 'Horario eliminado.';
        } else {
            return 'No se pudo eliminar.';
        }
    }

    // Actualizar Horario
    public function update($id, Request $request)
    {
        $params = $request->all();
        $horario = Horario::find($id)->update([
            'dia' => $params['dia'],
            'hora_inicio' => $params['hora_inicio'],
            'hora_fin' => $params['hora_fin']
        ]);
        return $horario ? 'El horario fue actualizado.' : 'No se pudo actualizar el horario.';
    }
}
