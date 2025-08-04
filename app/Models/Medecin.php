<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Medecin
 * 
 * @property int $idMedecin
 * @property string|null $Nom
 * @property string $Contact
 * @property Carbon|null $DtAjout
 * @property int $fkidcabinet
 *
 * @package App\Models
 */
class Medecin extends Model
{
	protected $table = 'medecins';
	protected $primaryKey = 'idMedecin';
	public $timestamps = false;


	protected $casts = [
		'DtAjout' => 'datetime',
		'fkidcabinet' => 'int'
	];

	protected $fillable = [
		'Nom',
		'Contact',
		'DtAjout',
		'fkidcabinet'
	];

	/**
	 * Relation avec le cabinet
	 */
	public function cabinet()
	{
		return $this->belongsTo(Cabinet::class, 'fkidcabinet', 'idCabinet');
	}
}
