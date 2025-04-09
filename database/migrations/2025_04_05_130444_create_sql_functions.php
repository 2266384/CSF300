<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Seeder to generate Stored Procedures that need to be included in the database
         */

        $db_type = DB::connection()->getDriverName();

        if ($db_type === 'sqlsrv') {

            /**
             * Function to remove non-alphanumeric characters - to be used during API Address Search
             */
            // Drop the function if it already exists
            DB::statement("IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'dbo.RemoveNonAlphaNumeric') AND type = 'FN')
                                    DROP FUNCTION dbo.RemoveNonAlphaNumeric;");

            // Recreate the function
            DB::statement("CREATE FUNCTION dbo.RemoveNonAlphaNumeric(@input NVARCHAR(MAX))
                                    RETURNS NVARCHAR(MAX)
                                    AS
                                    BEGIN
                                        WHILE PATINDEX('%[^a-zA-Z0-9]%', @input) > 0
                                        SET @input = STUFF(@input, PATINDEX('%[^a-zA-Z0-9]%', @input), 1, '');

                                        RETURN @input;
                                    END");


            /**
             * These functions are used for creating our training dataset of Random Forest Model
             */
            // Drop the function if it already exists
            DB::statement("IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'dbo.ShortenStreetName') AND type = 'FN')
                                    DROP FUNCTION dbo.ShortenStreetName;");

            // Recreate the function
            DB::statement("CREATE FUNCTION [dbo].[ShortenStreetName](@input NVARCHAR(MAX))
                                    RETURNS NVARCHAR(MAX)
                                    AS
                                    BEGIN

                                        declare @output NVARCHAR(MAX) =
                                            replace(
                                                replace(
                                                    replace(
                                                        replace(
                                                            replace(
                                                                replace(
                                                                    replace(
                                                                        replace(
                                                                            replace(
                                                                                replace(
                                                                                    replace(
                                                                                        replace(
                                                                                            replace(
                                                                                                replace(@input
                                                                                                ,'STREET'       ,'ST')
                                                                                            ,'ROAD'         ,'RD')
                                                                                        ,'AVENUE'       ,'AVE')
                                                                                    ,'LANE'         ,'LN')
                                                                                ,'DRIVE'        ,'DR')
                                                                            ,'CLOSE'        ,'CLS')
                                                                        ,'CRESCENT'     ,'CRES')
                                                                    ,'COURT'        ,'CT')
                                                                ,'PLACE'        ,'PL')
                                                            ,'TERRACE'      ,'TER')
                                                        ,'GARDENS'      ,'GDNS')
                                                    ,'GROVE'        ,'GR')
                                                ,'SQUARE'       ,'SQ')
                                            ,'PARK'         ,'PK')

                                        RETURN @output;
                                    END");

            // Drop the function if it already exists
            DB::statement("IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'dbo.LevenshteinDistance') AND type = 'FN')
                                    DROP FUNCTION dbo.LevenshteinDistance;");

            // Recreate the function
            DB::statement("CREATE FUNCTION [dbo].[LevenshteinDistance](@s1 NVARCHAR(MAX), @s2 NVARCHAR(MAX))
                                    RETURNS INT
                                    AS
                                    BEGIN
                                        DECLARE @lenS1 INT = LEN(@s1), @lenS2 INT = LEN(@s2);
                                        DECLARE @i INT, @j INT, @cost INT;

                                        -- Edge cases: If either string is empty, distance is the length of the other
                                        IF @lenS1 = 0 RETURN @lenS2;
                                        IF @lenS2 = 0 RETURN @lenS1;

                                        -- Table to store distances
                                        DECLARE @dist TABLE (i INT, j INT, cost INT PRIMARY KEY (i, j));

                                        -- Initialize first row (deletions)
                                        SET @i = 0;
                                        WHILE @i <= @lenS1
                                        BEGIN
                                            INSERT INTO @dist (i, j, cost) VALUES (@i, 0, @i);
                                            SET @i = @i + 1;
                                        END

                                        -- Initialize first column (insertions)
                                        SET @j = 0;
                                        WHILE @j <= @lenS2
                                        BEGIN
                                            IF NOT EXISTS (SELECT 1 FROM @dist WHERE i = 0 AND j = @j)
                                                INSERT INTO @dist (i, j, cost) VALUES (0, @j, @j);
                                            SET @j = @j + 1;
                                        END

                                        -- Compute distances
                                        SET @i = 1;
                                        WHILE @i <= @lenS1
                                        BEGIN
                                            SET @j = 1;
                                            WHILE @j <= @lenS2
                                            BEGIN
                                                -- Cost of substitution (0 if equal, 1 if different)
                                                SET @cost = CASE WHEN SUBSTRING(@s1, @i, 1) = SUBSTRING(@s2, @j, 1) THEN 0 ELSE 1 END;

                                                -- Get previous costs
                                                DECLARE @del INT, @ins INT, @sub INT;

                                                SELECT @del = cost FROM @dist WHERE i = @i - 1 AND j = @j;  -- Deletion
                                                SELECT @ins = cost FROM @dist WHERE i = @i AND j = @j - 1;  -- Insertion
                                                SELECT @sub = cost FROM @dist WHERE i = @i - 1 AND j = @j - 1;  -- Substitution

                                                -- Compute minimum cost manually
                                                DECLARE @minCost INT;
                                                SET @minCost = @del + 1;  -- Assume deletion is the smallest

                                                IF @ins + 1 < @minCost SET @minCost = @ins + 1;  -- Insertion
                                                IF @sub + @cost < @minCost SET @minCost = @sub + @cost;  -- Substitution

                                                -- Insert calculated cost
                                                INSERT INTO @dist (i, j, cost) VALUES (@i, @j, @minCost);

                                                SET @j = @j + 1;
                                            END
                                            SET @i = @i + 1;
                                        END

                                        -- Return final Levenshtein distance
                                        DECLARE @result INT;
                                        SELECT @result = cost FROM @dist WHERE i = @lenS1 AND j = @lenS2;
                                        RETURN @result;
                                        END;");

        } else if ($db_type === 'mysql') {

            /**
             * Function to remove non-alphanumeric characters - to be used during API Address Search
             */
            DB::statement("
                DROP FUNCTION IF EXISTS RemoveNonAlphaNumeric;
            ");

            DB::statement("
                CREATE FUNCTION RemoveNonAlphaNumeric(input_text TEXT)
                RETURNS TEXT DETERMINISTIC
                BEGIN
                    DECLARE output_text TEXT;
                    SET output_text = input_text;

                    WHILE output_text REGEXP '[^a-zA-Z0-9]' DO
                        SET output_text = REGEXP_REPLACE(output_text, '[^a-zA-Z0-9]', '');
                    END WHILE;

                    RETURN output_text;
                END
            ");

        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('stored_procedures');
    }
};
