<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest; use Illuminate\Validation\Rule;
final class UpdateTicketRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['subject'=>['sometimes','required','string','max:120'],'description'=>['sometimes','nullable','string','max:5000'],'priority'=>['sometimes',Rule::in(['low','normal','high','urgent'])]];} }
