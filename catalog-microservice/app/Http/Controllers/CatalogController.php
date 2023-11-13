<?php

namespace App\Http\Controllers;

class CatalogController extends Controller
{
    public function index()
    {
        $data = [];
        if (($handle = fopen(storage_path('\app\catalog.csv'), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = [
                    'id' => $row[0],
                    'title' => $row[1],
                    'quantity' => $row[2],
                    'price' => $row[3],
                    'topic' => $row[4],
                ];
            }
            fclose($handle);
        }

        return response()->json(['catalog' => $data]);
    }

    public function show($id)
    {
        // Ensure the item number is a valid integer
        if (!is_numeric($id)) {
            return response()->json(['error' => 'Invalid item number.'], 400);
        }

        // Initialize a variable to store the found book
        $foundBook = null;

        if (($handle = fopen(storage_path('\app\catalog.csv'), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if ($row[0] == $id) {
                    $foundBook = [
                        'id' => $row[0],
                        'title' => $row[1],
                        'quantity' => $row[2],
                        'price' => $row[3],
                        'topic' => $row[4],
                    ];
                    break;
                }
            }
            fclose($handle);

            if ($foundBook) {
                return response()->json(['book' => $foundBook]);
            } else {
                return response()->json(['error' => 'Book not found.'], 404);
            }
        } else {
            return response()->json(['error' => 'Failed to open the catalog file.'], 500);
        }
    }


    public function update($id)
    {
        $data = request()->all();

        $rows = [];
        if (($handle = fopen(storage_path('\app\catalog.csv'), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if ($row[0] == $id) {
                    $row = [$id, $data['title'], $data['quantity'], $data['price'], $data['topic']];
                }
                $rows[] = $row;
            }
            fclose($handle);
        }else {
            return response()->json(['error' => 'Failed to open the catalog file.'], 500);
        }

        if (($handle = fopen(storage_path('catalog.csv'), 'w')) !== false) {
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }

        return response()->json(['message' => 'Catalog item updated']);
    }

}
