outputFile="./validImage.res"
validImg=("png" "jpg" "gif")
validFiles=()

filesInCurrentDir=`ls`
for file in $filesInCurrentDir; do
    ext=`sed 's/^\w\+.//' <<< "$file"`
    #echo "the extention for $file is: "$ext

    for valid in "${validImg[@]}"; do
        if [ "$ext" == "$valid" ];
            then 
                validFiles+=($file)
                break;
        fi
    done
done
#clears
rm $outputFile
for validF in "${validFiles[@]}"; do
    echo "$validF" >> $outputFile
done

