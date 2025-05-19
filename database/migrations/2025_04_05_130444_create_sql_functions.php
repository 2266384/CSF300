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
                                            ,'YARD'         ,'YD'
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

            // Drop the function if it already exists
            DB::statement("
                DROP FUNCTION IF EXISTS LevenshteinDistance;
            ");

            // Recreate the function
            DB::statement("
            CREATE FUNCTION LevenshteinDistance(s1 VARCHAR(255), s2 VARCHAR(255))
            RETURNS INT
            DETERMINISTIC
            BEGIN
                DECLARE s1_len, s2_len, i, j, cost INT;
                DECLARE last_diag, old_diag, insert_cost, delete_cost, replace_cost, min_cost INT;
                DECLARE s1_char CHAR(1);
                DECLARE cv0, cv1 TEXT;

                SET s1_len = CHAR_LENGTH(s1);
                SET s2_len = CHAR_LENGTH(s2);

                -- Handle edge cases
                IF s1_len = 0 THEN RETURN s2_len; END IF;
                IF s2_len = 0 THEN RETURN s1_len; END IF;

                SET cv1 = '';
                SET i = 0;
                WHILE i <= s2_len DO
                    SET cv1 = CONCAT(cv1, i, ',');
                    SET i = i + 1;
                END WHILE;

                SET i = 1;
                WHILE i <= s1_len DO
                    SET s1_char = SUBSTRING(s1, i, 1);
                    SET cv0 = cv1;
                    SET cv1 = CONCAT(i, ',');
                    SET j = 1;

                    WHILE j <= s2_len DO
                        SET cost = IF(s1_char = SUBSTRING(s2, j, 1), 0, 1);

                        SET insert_cost = CAST(NULLIF(SUBSTRING_INDEX(SUBSTRING_INDEX(cv1, ',', j), ',', -1), '') AS UNSIGNED) + 1;
                        SET delete_cost = CAST(NULLIF(SUBSTRING_INDEX(SUBSTRING_INDEX(cv0, ',', j + 1), ',', -1), '') AS UNSIGNED) + 1;
                        SET replace_cost = CAST(NULLIF(SUBSTRING_INDEX(SUBSTRING_INDEX(cv0, ',', j), ',', -1), '') AS UNSIGNED) + cost;

                        SET min_cost = LEAST(insert_cost, delete_cost, replace_cost);
                        SET cv1 = CONCAT(cv1, min_cost, ',');

                        SET j = j + 1;
                    END WHILE;

                    SET i = i + 1;
                END WHILE;

                RETURN CAST(NULLIF(SUBSTRING_INDEX(SUBSTRING_INDEX(cv1, ',', s2_len + 1), ',', -1), '') AS UNSIGNED);
            END;
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
