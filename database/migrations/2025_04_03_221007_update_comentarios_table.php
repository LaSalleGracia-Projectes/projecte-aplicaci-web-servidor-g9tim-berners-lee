use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateComentariosTable extends Migration
{
    public function up()
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropColumn('fecha_creacion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->timestamp('fecha_creacion')->useCurrent();
        });
    }
}
