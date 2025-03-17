<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Kebun</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="text-center">Pilih Kebun</h3>
                <form method="POST" action="{{ url('/pilih-kebun') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="kebun" class="form-label">Kebun</label>
                        <select name="kebun_id" class="form-control">
                            <option value="">Pilih Kebun</option>
                            @foreach($kebunList as $kebun)
                                <option value="{{ $kebun->KodeKebun }}">{{ $kebun->NamaKebun }}</option>
                            @endforeach
                        </select>
                        
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Masuk Aplikasi</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>