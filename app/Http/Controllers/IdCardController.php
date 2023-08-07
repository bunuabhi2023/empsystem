<?php

namespace App\Http\Controllers;

use App\IdCard;
use App\InvoiceItem;
use App\Notifications\InvoicePaidNotification;
use App\Notifications\InvoiceReceivedNotification;
use App\Project;
use App\TaxType;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;


class IdCardController extends Controller {

    public function index()
    {
        $templates = IdCard::all();
        return view('templates.id-cards.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.id-cards.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'template_html' => 'required|string',
        ]);

        IdCard::create($validatedData);

        return redirect()->route('templates.id-cards.index')
                        ->with('success', 'Template created successfully');
    }

    public function edit(IdCard $template)
    {
        return view('templates.id-cards.edit', compact('template'));
    }

    public function update(Request $request, IdCard $template)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'template_html' => 'required|string',
        ]);

        $template->update($validatedData);

        return redirect()->route('templates.id-cards.index')
                        ->with('success', 'Template updated successfully');
    }

    public function delete(IdCard $template)
    {
        $template->delete();

        return redirect()->route('templates.id-cards.index')
                        ->with('success', 'Template deleted successfully');
    }
}