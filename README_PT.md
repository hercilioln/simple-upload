# SimpleUpload para Laravel

Um pacote leve e robusto para gerenciar uploads de arquivos no Laravel.

Ele abstrai a lógica repetitiva de **verificar, deletar o antigo e subir o novo**, mantendo seu código limpo e seguindo o princípio DRY.

Funciona perfeitamente com **Local** e **Amazon S3** (ou qualquer driver configurado no seu filesystem).

## 🚀 Instalação

```bash
composer require hercilio/simple-upload
```

## ⚙️ Configuração

O pacote utiliza automaticamente o disco definido no seu `.env`.

```env
# Para usar localmente
FILESYSTEM_DISK=local

# Para usar S3
FILESYSTEM_DISK=s3
```

## 💡 Como Usar

Primeiro, importe a Facade no seu Controller:

```php
use Hercilio\SimpleUpload\Facades\SimpleUpload;
```

### 1. Upload Simples (Gera Hash Único)

Ideal para avatares e imagens onde o nome original não importa.

```php
// Salva em storage/app/avatars/hash-unico.jpg
$path = SimpleUpload::upload($request->file('avatar'), 'avatars');
```

### 2. Upload com Nome Personalizado

Você pode especificar um nome customizado para o arquivo (sem a extensão):

```php
// Salvo como: avatars/foto-perfil.jpg
$path = SimpleUpload::upload(
    $request->file('avatar'), 
    'avatars', 
    'foto-perfil'
);
```

### 3. Upload Mantendo o Nome Original (Sanitizado) ✨

Ideal para documentos (PDFs, planilhas) onde você quer preservar o nome do arquivo. O pacote remove acentos e espaços automaticamente.

```php
// Arquivo enviado: "Relatório Financeiro 2026.pdf"
// Salva como: "docs/relatorio-financeiro-2026.pdf"
$path = SimpleUpload::uploadAsOriginal($request->file('doc'), 'docs');
```

### 4. Forçando um Disco Específico (Opcional)

Se você precisa salvar em um disco diferente do padrão, passe-o como **último parâmetro**:

```php
// Com nome customizado e disco específico
SimpleUpload::upload($file, 'backups', 'backup-mensal', 's3');

// Sem nome customizado, apenas disco específico
SimpleUpload::upload($file, 'backups', null, 's3');

// Upload como original com disco específico
SimpleUpload::uploadAsOriginal($file, 'docs', 's3');
```

### 5. A "Killer Feature": Update sem Dor de Cabeça 🔥

Substituir um arquivo é chato: você tem que checar se o novo existe, deletar o antigo, etc. O `SimpleUpload` faz tudo em uma linha.

```php
public function update(Request $request, User $user)
{
    // Se o usuário enviou uma nova foto:
    // 1. Deleta a foto antiga ($user->foto_path)
    // 2. Faz upload da nova
    // 3. Retorna o novo caminho
    // Se não enviou nada, retorna o caminho antigo intacto.
    
    $path = SimpleUpload::update(
        $request->file('foto'), 
        $user->foto_path, 
        'usuarios'
    );
    
    $user->update(['foto_path' => $path]);
}
```

Você também pode especificar um disco para o update:

```php
$path = SimpleUpload::update(
    $request->file('foto'), 
    $user->foto_path, 
    'usuarios',
    's3'
);
```

## 📋 Assinaturas dos Métodos

```php
// Método principal de upload
upload(?UploadedFile $file, string $folder = 'uploads', ?string $customName = null, ?string $disk = null)

// Upload com nome original
uploadAsOriginal(?UploadedFile $file, string $folder = 'uploads', ?string $disk = null)

// Atualizar arquivo existente
update(?UploadedFile $newFile, ?string $currentPath, string $folder = 'uploads', ?string $disk = null)

// Deletar arquivo
delete(?string $path, ?string $disk = null)
```

## 📝 Licença

MIT License. Sinta-se livre para usar em seus projetos pessoais e comerciais.
